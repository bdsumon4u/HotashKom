<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_id', 'name', 'slug', 'is_enabled',
    ];

    #[\Override]
    public static function booted(): void
    {
        static::saved(function ($brand): void {
            cache()->memo()->forget('brands');

            // Dispatch job to copy brand to reseller databases
            if (isOninda() && $brand->wasRecentlyCreated) {
                dispatch(new \App\Jobs\CopyResourceToResellers($brand));
            }
        });

        static::deleting(function ($brand): void {
            throw_if(isReseller() && $brand->source_id !== null, \Exception::class, 'Cannot delete a resource that has been sourced.');

            // Dispatch job to remove brand from reseller databases
            if (isOninda()) {
                dispatch(new \App\Jobs\RemoveResourceFromResellers($brand->getTable(), $brand->id));
            }
            cache()->memo()->forget('brands');
        });
    }

    public static function cached()
    {
        return cache()->memo()->rememberForever('brands', fn () => Brand::where('is_enabled', true)->get());
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
