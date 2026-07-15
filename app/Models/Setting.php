<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Setting extends Model
{
    protected $fillable = [
        'name', 'value',
    ];

    #[\Override]
    public static function booted(): void
    {
        static::saved(function ($setting): void {
            cacheMemo()->put('settings:'.$setting->name, $setting->value);
            cacheMemo()->forget('settings');
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

            // Ensure delivery_areas exists
            if (empty($settings['delivery_areas'])) {
                $insideCost = isset($settings['delivery_charge']) ? ($settings['delivery_charge']->inside_dhaka ?? 60) : 60;
                $outsideCost = isset($settings['delivery_charge']) ? ($settings['delivery_charge']->outside_dhaka ?? 120) : 120;

                $settings['delivery_areas'] = [
                    ['name' => 'Inside Dhaka', 'cost' => (int) $insideCost, 'is_default' => true],
                    ['name' => 'Outside Dhaka', 'cost' => (int) $outsideCost, 'is_default' => false],
                ];
            }

            // For backwards compatibility, reconstruct/override delivery_charge and default_area
            $insideArea = collect($settings['delivery_areas'])->first(fn ($a) => Str::contains(Str::lower(data_get($a, 'name') ?? ''), 'inside') || Str::contains(Str::lower(data_get($a, 'name') ?? ''), 'ঢাকা শহর') || Str::contains(Str::lower(data_get($a, 'name') ?? ''), 'ঢাকা সিটি'));
            $insideArea ??= $settings['delivery_areas'][0] ?? null;

            $outsideArea = collect($settings['delivery_areas'])->first(fn ($a) => Str::contains(Str::lower(data_get($a, 'name') ?? ''), 'outside') || Str::contains(Str::lower(data_get($a, 'name') ?? ''), 'বাহির'));
            $outsideArea ??= collect($settings['delivery_areas'])->first(fn ($a) => ! $insideArea || (data_get($a, 'name') !== data_get($insideArea, 'name')));
            $outsideArea ??= $settings['delivery_areas'][1] ?? $settings['delivery_areas'][0] ?? null;

            $settings['delivery_charge'] = (object) [
                'inside_dhaka' => (int) data_get($insideArea, 'cost', 60),
                'outside_dhaka' => (int) data_get($outsideArea, 'cost', 120),
            ];

            $settings['default_area'] = (object) [
                'inside' => (bool) data_get($insideArea, 'is_default', false),
                'outside' => (bool) data_get($outsideArea, 'is_default', false),
            ];

            return $settings;
        });
    }

    protected function value(): Attribute
    {
        return Attribute::make(
            fn ($value): mixed => json_decode((string) $value),
            fn ($value) => $this->attributes['value'] = json_encode(
                (is_array($value) && ! array_is_list($value)) ? array_merge((array) $this->value, $value) : $value
            ),
        );
    }
}
