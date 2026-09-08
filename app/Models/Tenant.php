<?php

namespace App\Models;

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
        return $this->hasMany(Admin::class, 'tenant_id');
    }

    /**
     * Settings belonging to this tenant.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class, 'tenant_id');
    }

    /**
     * Products belonging to this tenant.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'tenant_id');
    }

    /**
     * Orders belonging to this tenant.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'tenant_id');
    }
}
