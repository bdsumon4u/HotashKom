<?php

namespace App\Models;

use App\Jobs\CopyResourceToResellers;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'image_id', 'name', 'slug',
    ];

    public static function booted(): void
    {
        static::saved(function ($brand): void {
            cache()->forget('brands');

            // Dispatch job to copy brand to reseller databases
            if ($brand->wasRecentlyCreated) {
                CopyResourceToResellers::dispatch($brand, 'slug');
            }
        });

        static::deleting(function (): void {
            cache()->forget('brands');
        });
    }

    public static function cached()
    {
        return cache()->rememberForever('brands', fn () => Brand::all());
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class)
            ->whereNull('parent_id');
    }
}
