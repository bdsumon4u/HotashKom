<?php

namespace App\Models;

use App\Jobs\CopyResourceToResellers;
use App\Jobs\RemoveResourceFromResellers;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $guarded = ['id'];

    public static function booted(): void
    {
        static::saved(function ($option): void {
            // Dispatch job to copy option to reseller databases
            if (isOninda() && $option->wasRecentlyCreated) {
                CopyResourceToResellers::dispatch($option);
            }
        });

        static::deleting(function ($option): void {
            if (! isOninda() && $option->source_id !== null) {
                throw new \Exception('Cannot delete a resource that has been sourced.');
            }

            // Dispatch job to remove option from reseller databases
            if (isOninda()) {
                RemoveResourceFromResellers::dispatch($option->getTable(), $option->id);
            }
        });
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
