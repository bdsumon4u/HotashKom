<?php

namespace App\Actions\Tenant;

use App\Models\Setting;
use App\Models\Tenant;

class ProvisionTenantSettings
{
    /**
     * Provision default settings for a newly created tenant.
     *
     * If this is the very first tenant, clone baseline settings from the central platform.
     * If existing tenants exist, clone settings from the first (reference/demo) tenant.
     */
    public function execute(Tenant $tenant): void
    {
        // Find existing reference tenant (excluding current tenant)
        $referenceTenant = Tenant::query()
            ->where('id', '!=', $tenant->id)
            ->orderBy('created_at', 'asc')
            ->first();

        if ($referenceTenant) {
            // Clone from the first demo/reference tenant
            $sourceSettings = Setting::withoutTenancy()
                ->where('tenant_id', $referenceTenant->id)
                ->get();

            // Fallback to central platform settings if reference tenant has none
            if ($sourceSettings->isEmpty()) {
                $sourceSettings = Setting::withoutTenancy()
                    ->whereNull('tenant_id')
                    ->get();
            }
        } else {
            // First tenant: Clone from central platform baseline
            $sourceSettings = Setting::withoutTenancy()
                ->whereNull('tenant_id')
                ->get();
        }

        foreach ($sourceSettings as $setting) {
            Setting::withoutTenancy()->updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'name' => $setting->name,
                ],
                [
                    'value' => $setting->value,
                ]
            );
        }
    }
}
