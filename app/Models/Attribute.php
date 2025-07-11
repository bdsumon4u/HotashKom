<?php

namespace App\Models;

use App\Jobs\CopyResourceToResellers;
use App\Jobs\RemoveResourceFromResellers;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $guarded = ['id'];

    public static function booted(): void
    {
        static::saved(function ($attribute): void {
            // Dispatch job to copy attribute to reseller databases
            if (isOninda() && $attribute->wasRecentlyCreated) {
                CopyResourceToResellers::dispatch($attribute);
            }
        });

        static::deleting(function ($record): void {
            if (!isOninda() && $record->source_id !== null) {
                throw new \Exception('Cannot delete a resource that has been sourced.');
            }

            // Dispatch job to remove attribute from reseller databases
            if (isOninda()) {
                RemoveResourceFromResellers::dispatch($record->getTable(), $record->id);
            }
        });
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }
}
