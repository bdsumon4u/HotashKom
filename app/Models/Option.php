<?php

namespace App\Models;

use App\Jobs\CopyResourceToResellers;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $guarded = ['id'];

    public static function booted(): void
    {
        static::saved(function ($option): void {
            // Dispatch job to copy option to reseller databases
            if ($option->wasRecentlyCreated) {
                CopyResourceToResellers::dispatch($option, 'name');
            }
        });
    }
}
