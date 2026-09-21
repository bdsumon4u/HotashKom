<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ImageTenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! config('tenancy.enabled', false)) {
            return;
        }

        if (function_exists('tenancy') && tenancy()->initialized) {
            $column = $model->qualifyColumn('tenant_id');
            $builder->where(function (Builder $query) use ($column): void {
                $query->where($column, tenant()->getTenantKey())
                    ->orWhereNull($column);
            });
        }
    }

    public function extend(Builder $builder): void
    {
        $builder->macro('withoutTenancy', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }
}
