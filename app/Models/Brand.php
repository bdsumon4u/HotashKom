<?php

namespace App\Models;

use App\Jobs\CopyResourceToResellers;
use App\Jobs\RemoveResourceFromResellers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Brand extends Model
{
    use HasFactory;
    use HasSEO;

    protected $fillable = [
        'image_id', 'name', 'slug', 'is_enabled',
    ];

    #[\Override]
    public static function booted(): void
    {
        static::created(function ($brand): void {
            static::clearBrandCaches();
        });

        static::saved(function ($brand): void {
            static::clearBrandCaches();

            // Dispatch job to copy brand to reseller databases
            if (isOninda() && $brand->wasRecentlyCreated) {
                dispatch(new CopyResourceToResellers($brand));
            }
        });

        static::deleting(function ($brand): void {
            // throw_if(isReseller() && $brand->source_id !== null, \Exception::class, 'Cannot delete a resource that has been sourced.');

            // Dispatch job to remove brand from reseller databases
            if (isOninda()) {
                dispatch(new RemoveResourceFromResellers($brand->getTable(), $brand->id));
            }
        });

        static::deleted(function (): void {
            static::clearBrandCaches();
        });
    }

    /**
     * Clear all brand-related caches.
     */
    private static function clearBrandCaches(): void
    {
        // Clear brands cache
        cacheMemo()->forget('brands');

        // Clear product filter data since brands are part of filters
        cacheMemo()->forget('product_filter_data');

        // Clear home sections cache (brands might be used in sections)
        cacheMemo()->forget('homesections');

        // Clear namespaced caches
        cacheInvalidateNamespace('api_sections');
        cacheInvalidateNamespace('product_filters');
    }

    public static function cached()
    {
        return cacheMemo()->rememberForever('brands', fn () => Brand::where('is_enabled', true)->get());
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class)
            ->whereNull('parent_id');
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
}
