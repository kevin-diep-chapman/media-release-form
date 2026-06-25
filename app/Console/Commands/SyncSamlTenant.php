<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Slides\Saml2\Helpers\Uuid;
use Slides\Saml2\Models\Tenant;

class SyncSamlTenant extends Command
{
    protected $signature = 'saml2:sync-tenant
                            {--key=chapman : Tenant key stored in the database}
                            {--show-credentials : Print SP URLs for IdP configuration}';

    protected $description = 'Create or update the SAML IdP tenant from environment variables';

    public function handle(): int
    {
        $entityId = env('SAML2_IDP_ENTITY_ID');
        $loginUrl = env('SAML2_IDP_LOGIN_URL');
        $logoutUrl = env('SAML2_IDP_LOGOUT_URL');
        $x509cert = env('SAML2_IDP_X509_CERT');

        if (! $entityId || ! $loginUrl || ! $logoutUrl || ! $x509cert) {
            $this->error('Set SAML2_IDP_ENTITY_ID, SAML2_IDP_LOGIN_URL, SAML2_IDP_LOGOUT_URL, and SAML2_IDP_X509_CERT in .env first.');

            return self::FAILURE;
        }

        $key = $this->option('key');
        $tenant = Tenant::query()->where('key', $key)->first();

        $payload = [
            'key' => $key,
            'idp_entity_id' => $entityId,
            'idp_login_url' => $loginUrl,
            'idp_logout_url' => $logoutUrl,
            'idp_x509_cert' => $this->normalizeCertificate($x509cert),
            'relay_state_url' => env('SAML2_LOGIN_URL', '/my-events'),
            'name_id_format' => env('SAML2_NAME_ID_FORMAT', 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent'),
            'metadata' => [],
        ];

        if ($tenant) {
            $tenant->update($payload);
            $this->info("Updated SAML tenant \"{$key}\" ({$tenant->uuid}).");
        } else {
            $tenant = Tenant::query()->create([
                ...$payload,
                'uuid' => Uuid::uuid7(),
            ]);
            $this->info("Created SAML tenant \"{$key}\" ({$tenant->uuid}).");
        }

        $this->newLine();
        $this->line('Add this to your .env file:');
        $this->line('SAML2_TENANT_UUID='.$tenant->uuid);

        if ($this->option('show-credentials')) {
            $this->newLine();
            $this->call('saml2:tenant-credentials', ['id' => $tenant->id]);
        }

        return self::SUCCESS;
    }

    private function normalizeCertificate(string $certificate): string
    {
        return trim(str_replace(
            ['-----BEGIN CERTIFICATE-----', '-----END CERTIFICATE-----', "\n", "\r"],
            '',
            $certificate,
        ));
    }
}
