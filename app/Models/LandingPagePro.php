<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandingPagePro extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'template_key',
        'is_published',
        'seo',
        'section_settings',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'seo' => 'array',
            'section_settings' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(LandingPageProItem::class)->orderBy('sort_order');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function defaultSectionSettings(): array
    {
        return [
            'announcement_bar' => ['enabled' => true],
            'header' => ['enabled' => true],
            'hero' => ['enabled' => true],
            'gallery' => ['enabled' => true],
            'cta_after_gallery' => ['enabled' => true],
            'video' => ['enabled' => true],
            'cta_after_video' => ['enabled' => true],
            'features' => ['enabled' => true],
            'size_guide' => ['enabled' => true],
            'cta_after_size_guide' => ['enabled' => true],
            'faq' => ['enabled' => true],
            'cta_after_faq' => ['enabled' => true],
            'order_form' => ['enabled' => true],
            'reviews' => ['enabled' => true],
            'final_cta' => ['enabled' => true],
            'footer' => ['enabled' => true],
        ];
    }

    public function mergedSectionSettings(): array
    {
        $stored = $this->section_settings ?? [];

        return array_replace_recursive(static::defaultSectionSettings(), is_array($stored) ? $stored : []);
    }
}
