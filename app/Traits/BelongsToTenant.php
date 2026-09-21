<?php

namespace App\Traits;

use App\Models\Tenant;
use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public static function bootBelongsToTenant(): void
    {
        if (! config('tenancy.enabled', false)) {
            return;
        }

        static::addGlobalScope(new TenantScope);

        static::creating(function ($model): void {
            if (! $model->getAttribute('tenant_id') && ! $model->relationLoaded('tenant')) {
                if (function_exists('tenancy') && tenancy()->initialized) {
                    $model->setAttribute('tenant_id', tenant()->getTenantKey());
                    $model->setRelation('tenant', tenant());
                }
            }
        });
    }
}
