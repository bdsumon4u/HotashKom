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

    public static function booted(): void
    {
        static::saved(function ($setting): void {
            Cache::put('settings:'.$setting->name, $setting);
            Cache::forget('settings');
        });
    }

    public static function array()
    {
        return Cache::rememberForever('settings', fn() => self::all()->flatMap(fn($setting) => [$setting->name => $setting->value])->toArray());
    }

    public function value(): Attribute
    {
        return Attribute::make(
            fn ($value): mixed => json_decode((string) $value),
            fn ($value) => $this->attributes['value'] = json_encode(
                is_array($value) ? array_merge((array) $this->value, $value) : $value
            ),
        );
    }
}
