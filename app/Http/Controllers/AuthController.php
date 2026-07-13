<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Slides\Saml2\Models\Tenant;

class AuthController extends Controller
{
    public function showLogin()
    {
        $samlTenantUuid = $this->resolveSamlTenantUuid();

        return view('auth.login', compact('samlTenantUuid'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/my-events');
        }

        return back()->withErrors(['email' => 'Credentials do not match our records.']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        Auth::login($user);

        return redirect('/my-events');
    }

    public function logout(Request $request)
    {
        $tenantUuid = $request->session()->get('saml2.tenant_uuid');

        if ($tenantUuid) {
            return redirect()->route('saml.logout', [
                'uuid' => $tenantUuid,
                'nameId' => $request->session()->get('saml2.name_id'),
                'sessionIndex' => $request->session()->get('saml2.session_index'),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    private function resolveSamlTenantUuid(): ?string
    {
        if ($uuid = env('SAML2_TENANT_UUID')) {
            return $uuid;
        }

        $tenantKey = env('SAML2_TENANT_KEY', 'chapman');

        return Tenant::query()->where('key', $tenantKey)->value('uuid');
    }
}
