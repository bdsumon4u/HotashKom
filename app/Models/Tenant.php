<?php

namespace App\Models;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    use HasDomains;

    public $incrementing = false;

    protected $keyType = 'string';

    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
    ];

    #[\Override]
    public static function booted(): void
    {
        static::deleting(function ($tenant): void {
            $tenant->admins()->delete();
            $tenant->settings()->delete();

            Product::withoutTenancy()->where('tenant_id', $tenant->id)->get()->each->delete();
            Category::withoutTenancy()->where('tenant_id', $tenant->id)->get()->each->delete();
            Brand::withoutTenancy()->where('tenant_id', $tenant->id)->get()->each->delete();
            LandingPagePro::withoutTenancy()->where('tenant_id', $tenant->id)->get()->each->delete();
            LandingPageProItem::withoutTenancy()->where('tenant_id', $tenant->id)->delete();
            Slide::withoutTenancy()->where('tenant_id', $tenant->id)->delete();
            HomeSection::withoutTenancy()->where('tenant_id', $tenant->id)->delete();
            Menu::withoutTenancy()->where('tenant_id', $tenant->id)->get()->each->delete();
            MenuItem::withoutTenancy()->where('tenant_id', $tenant->id)->delete();
            Page::withoutTenancy()->where('tenant_id', $tenant->id)->delete();
            Blog::withoutTenancy()->where('tenant_id', $tenant->id)->delete();
            Attribute::withoutTenancy()->where('tenant_id', $tenant->id)->get()->each->delete();
            Option::withoutTenancy()->where('tenant_id', $tenant->id)->delete();
        });
    }

    /**
     * Get custom columns that should be saved directly on the tenants table instead of JSON data.
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'created_at',
            'updated_at',
            'data',
        ];
    }

    /**
     * Admin users belonging to this tenant.
     */
    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class, 'tenant_id')
            ->withoutGlobalScope(TenantScope::class);
    }

    /**
     * Settings belonging to this tenant.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class, 'tenant_id')
            ->withoutGlobalScope(TenantScope::class);
    }

    /**
     * Products belonging to this tenant.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'tenant_id')
            ->withoutGlobalScope(TenantScope::class);
    }

    /**
     * Orders belonging to this tenant.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'tenant_id')
            ->withoutGlobalScope(TenantScope::class);
    }
}
