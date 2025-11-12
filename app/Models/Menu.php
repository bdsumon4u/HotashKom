<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $with = ['menuItems'];

    protected $fillable = [
        'name', 'slug',
    ];

    #[\Override]
    public static function booted(): void
    {
        static::saved(function ($menu): void {
            cacheMemo()->forget('menus:'.$menu->slug);
        });

        static::deleting(function ($menu): void {
            cacheMemo()->forget('menus:'.$menu->slug);
        });
    }

    #[\Override]
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
