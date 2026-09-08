<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $column = $model->qualifyColumn('tenant_id');

        if (config('tenancy.enabled', true) && function_exists('tenancy') && tenancy()->initialized) {
            $builder->where($column, tenant()->getTenantKey());
        } else {
            $builder->whereNull($column);
        }
    }

    public function extend(Builder $builder): void
    {
        $builder->macro('withoutTenancy', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }
}
