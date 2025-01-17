<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    protected $fillable = [
        'mobile_src', 'desktop_src', 'title', 'text', 'btn_name', 'btn_href', 'is_active',
    ];

    public static function booted()
    {
        static::saved(function ($menu): void {
            cache()->put('slides', static::whereIsActive(1)->get());
        });

        static::deleted(function (): void {
            cache()->forget('slides');
        });
    }
}
