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

    #[\Override]
    public static function booted(): void
    {
        static::saved(function ($setting): void {
            Cache::memo()->put('settings:'.$setting->name, $setting);
            Cache::forget('settings');
        });
    }

    public static function array()
    {
        return Cache::memo()->rememberForever('settings', fn () => self::all()->flatMap(fn ($setting): array => [$setting->name => $setting->value])->toArray());
    }

    protected function value(): Attribute
    {
        return Attribute::make(
            fn ($value): mixed => json_decode((string) $value),
            fn ($value) => $this->attributes['value'] = json_encode(
                is_array($value) ? array_merge((array) $this->value, $value) : $value
            ),
        );
    }
}
