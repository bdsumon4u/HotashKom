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
            cacheMemo()->put('settings:'.$setting->name, $setting);
            Cache::forget('settings');
        });
    }

    public static function array()
    {
        return cacheMemo()->rememberForever('settings', function () {
            $settings = self::all()->flatMap(fn ($setting): array => [$setting->name => $setting->value])->toArray();

            if (empty($settings['company'])) {
                $settings['company'] = (object) [
                    'name' => 'HotashKom',
                    'phone' => '01700000000',
                    'whatsapp' => '01700000000',
                    'email' => 'info@hotashkom.test',
                    'address' => 'Dhaka, Bangladesh',
                    'messenger' => '',
                ];
            }

            if (empty($settings['logo'])) {
                $settings['logo'] = (object) [
                    'desktop' => '',
                    'favicon' => '',
                ];
            }

            if (empty($settings['show_option'])) {
                $settings['show_option'] = (object) [
                    'customer_login' => false,
                ];
            }

            return $settings;
        });
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
