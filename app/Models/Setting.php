<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'name', 'value',
    ];

    public static function booted()
    {
        static::saved(function ($setting) {
            Cache::put('settings:'.$setting->name, $setting);
            Cache::forget('settings');
        });
    }

    public static function array()
    {
        return Cache::rememberForever('settings', function () {
            return self::all()->flatMap(function ($setting) {
                return [$setting->name => $setting->value];
            })->toArray();
        });
    }

    public function value(): Attribute
    {
        return Attribute::make(
            fn ($value) => json_decode($value),
            fn ($value) => $this->attributes['value'] = json_encode(
                is_array($value) ? array_merge((array) $this->value, $value) : $value
            ),
        );
    }
}
