<?php

namespace App\Actions\Tenant;

use App\Models\Admin;
use App\Models\Domain;
use App\Models\Setting;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateTenantAction
{
    public function __construct(
        protected ProvisionTenantSettings $provisionTenantSettings,
        protected CloneTenantResources $cloneTenantResources
    ) {}

    /**
     * Create a new tenant with domains, admin user, provisioned settings, and selective catalog resources.
     *
     * @param  array{
     *     id: string,
     *     name: string,
     *     custom_domain?: string|null,
     *     admin_name?: string|null,
     *     admin_email: string,
     *     admin_password?: string|null,
     *     clone_products?: bool,
     *     clone_categories?: bool,
     *     clone_brands?: bool,
     *     clone_landing_pages?: bool,
     *     clone_sliders?: bool,
     *     clone_home_sections?: bool,
     *     clone_menus?: bool,
     *     clone_pages?: bool,
     *     clone_blogs?: bool,
     * }  $data
     */
    public function execute(array $data): Tenant
    {
        return DB::transaction(function () use ($data) {
            $slug = Str::slug($data['id']);

            // 1. Create Tenant
            $tenant = Tenant::create([
                'id' => $slug,
                'data' => [
                    'name' => $data['name'] ?? $slug,
                ],
            ]);

            // 2. Create Default Subdomain
            $centralHost = parse_url(config('app.url', 'http://localhost'), PHP_URL_HOST) ?: 'localhost';
            $subdomain = "{$slug}.{$centralHost}";

            $tenant->domains()->create([
                'domain' => $subdomain,
            ]);

            // 3. Create Custom Domain if provided
            if (! empty($data['custom_domain'])) {
                $customDomain = preg_replace('#^https?://#', '', trim($data['custom_domain']));
                $customDomain = rtrim($customDomain, '/');

                if ($customDomain && $customDomain !== $subdomain) {
                    $tenant->domains()->create([
                        'domain' => $customDomain,
                    ]);
                }
            }

            // 4. Create Tenant Admin User
            $adminEmail = ! empty($data['admin_email']) ? $data['admin_email'] : "{$slug}@{$centralHost}";
            $adminPassword = ! empty($data['admin_password']) ? $data['admin_password'] : 'password';

            Admin::create([
                'tenant_id' => $tenant->id,
                'name' => ! empty($data['admin_name']) ? $data['admin_name'] : ($data['name'].' Admin'),
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'role_id' => Admin::ADMIN,
                'is_active' => true,
            ]);

            // 5. Provision Settings (from reference tenant or central platform)
            $this->provisionTenantSettings->execute($tenant);

            // 6. Clone Selective Catalog & Content Resources
            $this->cloneTenantResources->execute($tenant, $data);

            // 7. Update company name in provisioned settings
            if (! empty($data['name'])) {
                $companySetting = Setting::withoutTenancy()
                    ->where('tenant_id', $tenant->id)
                    ->where('name', 'company')
                    ->first();

                if ($companySetting) {
                    $companyData = is_array($companySetting->value) ? $companySetting->value : (array) $companySetting->value;
                    $companyData['name'] = $data['name'];
                    $companySetting->update(['value' => $companyData]);
                }
            }

            return $tenant;
        });
    }
}
