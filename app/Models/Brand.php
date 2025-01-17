<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'image_id', 'name', 'slug',
    ];

    public static function booted(): void
    {
        static::saved(function (): void {
            cache()->forget('brands');
        });

        static::deleting(function (): void {
            cache()->forget('brands');
        });
    }

    public static function cached()
    {
        return cache()->rememberForever('brands', fn() => Brand::all());
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
