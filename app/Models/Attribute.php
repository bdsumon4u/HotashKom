<?php

namespace App\Models;

use App\Jobs\CopyResourceToResellers;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $guarded = ['id'];

    public static function booted(): void
    {
        static::saved(function ($attribute): void {
            // Dispatch job to copy attribute to reseller databases
            if ($attribute->wasRecentlyCreated) {
                CopyResourceToResellers::dispatch($attribute, 'name');
            }
        });
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }
}
