<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Slides\Saml2\Events\SignedIn;

class SamlSignedIn
{
    public function handle(SignedIn $event): void
    {
        $messageId = $event->getAuth()->getLastMessageId();

        if ($messageId && Cache::has('saml2.message_id.'.$messageId)) {
            abort(403, 'SAML response has already been processed.');
        }

        if ($messageId) {
            Cache::put('saml2.message_id.'.$messageId, true, now()->addDay());
        }

        $samlUser = $event->getSaml2User();
        $attributes = $samlUser->getAttributes();

        $email = $this->firstAttributeValue($attributes, [
            'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress',
            'email',
            'mail',
            'EmailAddress',
        ]);

        if (! $email) {
            $nameId = $samlUser->getNameId();
            if (filter_var($nameId, FILTER_VALIDATE_EMAIL)) {
                $email = $nameId;
            }
        }

        if (! $email) {
            abort(403, 'No email address was provided by the identity provider.');
        }

        $email = Str::lower($email);

        $firstName = $this->firstAttributeValue($attributes, [
            'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname',
            'givenName',
            'first_name',
            'FirstName',
        ]);

        $lastName = $this->firstAttributeValue($attributes, [
            'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname',
            'sn',
            'last_name',
            'LastName',
        ]);

        $displayName = $this->firstAttributeValue($attributes, [
            'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name',
            'displayName',
            'name',
        ]);

        $title = $this->firstAttributeValue($attributes, [
            'title',
            'jobTitle',
        ]);

        $department = $this->firstAttributeValue($attributes, [
            'department',
            'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/department',
        ]);

        $name = $displayName ?: trim(($firstName ?? '').' '.($lastName ?? '')) ?: $email;

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update(array_filter([
                'name' => $name,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'title' => $title,
                'department' => $department,
            ], fn ($value) => $value !== null && $value !== ''));
        } else {
            $user = User::create([
                'name' => $name,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'title' => $title,
                'department' => $department,
                'password' => Str::password(32),
                'role' => 'user',
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user);
        request()->session()->regenerate();

        session([
            'saml2.tenant_uuid' => $samlUser->getTenant()?->uuid,
            'saml2.name_id' => $samlUser->getNameId(),
            'saml2.session_index' => $samlUser->getSessionIndex(),
        ]);
    }

    /**
     * @param  array<string, array<int, string>>  $attributes
     * @param  array<int, string>  $keys
     */
    private function firstAttributeValue(array $attributes, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (! empty($attributes[$key][0])) {
                return $attributes[$key][0];
            }
        }

        return null;
    }
}
