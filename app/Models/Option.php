<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $guarded = ['id'];

    #[\Override]
    public static function booted(): void
    {
        static::saved(function ($option): void {
            // Clear product filter data since options are part of filters
            cacheMemo()->forget('product_filter_data');
            // Clear attributes cache (since options are part of attributes)
            cacheMemo()->forget('attributes_with_options');

            // Dispatch job to copy option to reseller databases
            if (isOninda() && $option->wasRecentlyCreated) {
                dispatch(new \App\Jobs\CopyResourceToResellers($option));
            }
        });

        static::deleting(function ($option): void {
            // throw_if(isReseller() && $option->source_id !== null, \Exception::class, 'Cannot delete a resource that has been sourced.');

            // Clear product filter data since options are part of filters
            cacheMemo()->forget('product_filter_data');
            // Clear attributes cache (since options are part of attributes)
            cacheMemo()->forget('attributes_with_options');

            // Dispatch job to remove option from reseller databases
            if (isOninda()) {
                dispatch(new \App\Jobs\RemoveResourceFromResellers($option->getTable(), $option->id));
            }
        });
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
