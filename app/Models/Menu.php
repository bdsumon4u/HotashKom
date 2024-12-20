<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $with = ['menuItems'];

    protected $fillable = [
        'name', 'slug',
    ];

    public static function booted()
    {
        static::saved(function ($menu) {
            cache()->forget('menus:'.$menu->slug);
        });

        static::deleting(function ($menu) {
            cache()->forget('menus:'.$menu->slug);
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
