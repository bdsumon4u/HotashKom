<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ImageTenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $column = $model->qualifyColumn('tenant_id');

        if (config('tenancy.enabled', true) && function_exists('tenancy') && tenancy()->initialized) {
            $builder->where(function (Builder $query) use ($column): void {
                $query->where($column, tenant()->getTenantKey())
                    ->orWhereNull($column);
            });
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
