<?php

namespace App\Models;

use App\Jobs\CopyResourceToResellers;
use App\Jobs\RemoveResourceFromResellers;
use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Category extends Model
{
    use HasSEO;

    protected $fillable = [
        'parent_id', 'image_id', 'name', 'slug', 'order', 'is_enabled', 'content',
        'premium_header_config', 'premium_guide_config',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'premium_header_config' => 'array',
            'premium_guide_config' => 'array',
        ];
    }

    #[\Override]
    public static function booted(): void
    {
        static::created(function ($category): void {
            static::clearCategoryCaches($category);
        });

        static::saved(function ($category): void {
            static::clearCategoryCaches($category);

            // Dispatch job to copy category to reseller databases
            if (isOninda() && $category->wasRecentlyCreated) {
                dispatch(new CopyResourceToResellers($category));
            }
        });

        static::deleting(function ($category): void {
            // throw_if(isReseller() && $category->source_id !== null, \Exception::class, 'Cannot delete a resource that has been sourced.');

            // Dispatch job to remove category from reseller databases
            if (isOninda()) {
                dispatch(new RemoveResourceFromResellers($category->getTable(), $category->id));
            }
            $category->childrens->each->delete();
        });

        static::deleted(function ($category): void {
            static::clearCategoryCaches($category);
        });
    }

    /**
     * Clear all category-related caches.
     */
    private static function clearCategoryCaches($category): void
    {
        // Clear carousel cache
        cacheMemo()->forget('categories:carousel');

        // Clear nested categories cache (all variations)
        cacheMemo()->forget('categories:nested:');
        cacheMemo()->forget('categories:nested:enabled');
        cacheMemo()->forget('categories:nested:all');
        // Clear all possible nested cache variations
        for ($i = 0; $i <= 15; $i++) {
            cacheMemo()->forget("categories:nested:{$i}");
        }

        // Clear home sections cache (categories affect sections)
        cacheMemo()->forget('homesections');

        // Clear product filter data
        cacheMemo()->forget('product_filter_data');
        if ($category->id) {
            cacheMemo()->forget('product_filter_data:category:'.$category->id);
        }

        // Clear API category caches
        cacheMemo()->forget('api_categories:all');
        if ($category->slug) {
            cacheMemo()->forget('api_category:'.$category->slug);
        }
        // Clear all possible nested API category cache variations
        for ($i = 0; $i <= 15; $i++) {
            cacheMemo()->forget("api_categories:nested:{$i}");
        }

        // Clear namespaced caches
        cacheInvalidateNamespace('api_categories');
        cacheInvalidateNamespace('api_category');
        cacheInvalidateNamespace('api_sections');
        cacheInvalidateNamespace('product_filters');
        cacheInvalidateNamespace('related_products');
    }

    public function childrens()
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    public static function nested($count = 0, $enabledOnly = true)
    {
        $query = self::whereNull('parent_id')
            ->when($enabledOnly, fn ($query) => $query->where('is_enabled', true))
            ->with(['childrens' => function ($category) use ($enabledOnly): void {
                $category->when($enabledOnly, fn ($query) => $query->where('is_enabled', true))->with('childrens')->orderBy('order');
            }])
            ->withCount('childrens')
            ->orderBy('order');
        $count && $query->take($count);

        if ($count) {
            return cacheMemo()->rememberForever('categories:nested:'.$count, fn () => $query->get());
        }

        return cacheMemo()->rememberForever('categories:nested:'.($enabledOnly ? 'enabled' : 'all'), fn () => $query->get());
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function categoryMenu()
    {
        return $this->hasOne(CategoryMenu::class);
    }

    /**
     * Retrieve the model for route model binding.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        // Only decode URL-encoded value when the field is 'slug'
        if ($field === 'slug') {
            $decodedValue = rawurldecode((string) $value);

            return $this->where($field, $decodedValue)->first();
        }

        // For other fields (like 'id'), use the value as-is
        return $this->where($field, $value)->first();
    }

    /**
     * Get dynamic SEO data fallback.
     */
    public function getDynamicSEOData(): SEOData
    {
        $title = $this->seo?->title ?: $this->name;
        $description = $this->seo?->description;
        $image = $this->seo?->image;
        if (! $image && $this->image) {
            $image = $this->image->src;
        }

        return new SEOData(
            title: $title,
            description: $description,
            image: $image,
        );
    }

    /**
     * @return array{eyebrow: string|null, help_title: string|null, help_text: string|null, section_title: string|null, section_text: string|null}
     */
    public function getPremiumHeaderData(): array
    {
        $raw = $this->premium_header_config ?? [];
        $seoTitle = $this->seo?->title ?: $this->name;
        $seoDesc = $this->seo?->description ?? '';

        return [
            'eyebrow' => $raw['eyebrow'] ?? $this->name,
            'help_title' => $raw['help_title'] ?? $seoTitle,
            'help_text' => $raw['help_text'] ?? $seoDesc,
            'section_title' => $raw['section_title'] ?? ($this->name ? $this->name.'-এর বিভাগগুলো' : null),
            'section_text' => $raw['section_text'] ?? null,
        ];
    }

    /**
     * @return array{title: string, patterns: list<string>}|null
     */
    public function getPremiumGuideData(): ?array
    {
        $raw = $this->premium_guide_config ?? [];
        $patterns = $raw['patterns'] ?? [];

        if (empty($patterns) || ! is_array($patterns)) {
            return null;
        }

        return [
            'title' => $raw['title'] ?? ($this->name ? $this->name.' কেনার আগে আরও জানুন' : ''),
            'patterns' => array_values(array_filter($patterns)),
        ];
    }
}
