<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $guarded = ['id'];

    #[\Override]
    public static function booted(): void
    {
        static::saved(function ($attribute): void {
            // Dispatch job to copy attribute to reseller databases
            if (isOninda() && $attribute->wasRecentlyCreated) {
                dispatch(new \App\Jobs\CopyResourceToResellers($attribute));
            }
        });

        static::deleting(function ($record): void {
            // throw_if(isReseller() && $record->source_id !== null, \Exception::class, 'Cannot delete a resource that has been sourced.');

            // Dispatch job to remove attribute from reseller databases
            if (isOninda()) {
                dispatch(new \App\Jobs\RemoveResourceFromResellers($record->getTable(), $record->id));
            }
        });
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }
}
