<?php

namespace App\Models;

use Codebyray\ReviewRateable\Traits\ReviewRateable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Scout\Searchable;
use Nicolaslopezj\Searchable\SearchableTrait;
use RalphJSmit\Laravel\SEO\Support\HasSEO;

class Product extends Model
{
    use HasFactory;
    use HasSEO;
    use ReviewRateable;
    use Searchable;
    // use SearchableTrait;

    protected $with = ['images'];

    protected $fillable = [
        'brand_id', 'name', 'slug', 'description', 'short_description', 'price', 'average_purchase_price', 'selling_price', 'suggested_price', 'wholesale', 'sku',
        'source_id', 'should_track', 'stock_count', 'desc_img', 'desc_img_pos', 'is_active', 'hot_sale', 'new_arrival', 'shipping_inside', 'shipping_outside', 'delivery_text',
    ];

    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'products.sku' => 10,
            'products.name' => 8,
            'products.description' => 5,
        ],
    ];

    /**
     * The "booted" method of the model.
     */
    #[\Override]
    public static function booted(): void
    {
        static::created(function ($product): void {
            static::clearProductCaches($product);
        });

        static::saved(function ($product): void {
            static::clearProductCaches($product);

            // Dispatch job to sync stock attributes if they were changed
            if (isOninda() && $product->isDirty(['should_track', 'stock_count'])) {
                dispatch(new \App\Jobs\SyncProductStockWithResellers($product));
            }

            // Dispatch job to sync active status if it was changed
            if (isOninda() && $product->isDirty(['is_active'])) {
                dispatch(new \App\Jobs\SyncProductActiveWithResellers($product));
            }
        });

        static::deleting(function ($record): void {
            // throw_if(isReseller() && $record->source_id !== null, \Exception::class, 'Cannot delete a resource that has been sourced.');

            // Clear related caches before deletion
            static::clearProductCaches($record);

            // Dispatch job to remove product from reseller databases
            if (! $record->parent_id && isOninda()) { // not a variation
                dispatch(new \App\Jobs\RemoveResourceFromResellers($record->getTable(), $record->id));
            }
            $record->variations->each->delete();
        });

        static::deleted(function ($record): void {
            static::clearProductCaches($record);
        });

        static::addGlobalScope('latest', function (Builder $builder): void {
            $builder->latest('products.created_at');
        });
    }

    /**
     * Clear all product-related caches.
     */
    private static function clearProductCaches($product): void
    {
        // Clear general product filter data
        cacheMemo()->forget('product_filter_data');

        // Clear related products cache
        if ($product->slug) {
            cacheMemo()->forget('related_products:'.$product->slug);
            cacheInvalidateNamespace('related_products');
        }

        // Clear category-specific filter data for all categories this product belongs to
        $product->categories->each(function ($category) {
            cacheMemo()->forget('product_filter_data:category:'.$category->id);
        });

        // If this is a variation, also clear parent product caches
        if ($product->parent_id) {
            $parent = $product->parent;
            if ($parent) {
                cacheMemo()->forget('related_products:'.$parent->slug);
                cacheInvalidateNamespace('related_products');
                // Also clear category-specific caches for parent product's categories
                $parent->categories->each(function ($category) {
                    cacheMemo()->forget('product_filter_data:category:'.$category->id);
                });
            }
        }

        // Clear API-related caches
        cacheInvalidateNamespace('api_sections');
        cacheInvalidateNamespace('product_filters');
    }

    protected function varName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            if (! $this->parent_id) {
                return $this->name;
            }

            return $this->parent->name.' ['.$this->name.']';
        });
    }

    protected function shippingInside(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            if (! (setting('show_option')->productwise_delivery_charge ?? false)) {
                return setting('delivery_charge')->inside_dhaka;
            }

            if (! $this->parent_id) {
                return $value ?? setting('delivery_charge')->inside_dhaka;
            }

            return $this->parent->shipping_inside;
        });
    }

    protected function shippingOutside(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            if (! (setting('show_option')->productwise_delivery_charge ?? false)) {
                return setting('delivery_charge')->outside_dhaka;
            }

            if (! $this->parent_id) {
                return $value ?? setting('delivery_charge')->outside_dhaka;
            }

            return $this->parent->shipping_outside;
        });
    }

    protected function category(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            // Use already-loaded relationship if available to avoid N+1 queries
            if ($this->parent_id) {
                if ($this->relationLoaded('parent') && $this->parent && $this->parent->relationLoaded('categories')) {
                    return $this->parent->categories->first()?->name ?? 'Uncategorized';
                }

                return $this->parent->categories()->inRandomOrder()->first(['name'])->name ?? 'Uncategorized';
            }

            // Use already-loaded relationship if available
            if ($this->relationLoaded('categories')) {
                return $this->categories->first()?->name ?? 'Uncategorized';
            }

            return $this->categories()->inRandomOrder()->first(['name'])->name ?? 'Uncategorized';
        });
    }

    protected function inStock(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn () => $this->track_stock
            ? $this->stock_count
            : true);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->belongsToMany(Image::class)
            ->withPivot(['img_type', 'order'])
            ->orderBy('order')
            ->withTimestamps();
    }

    public function parent()
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function variations()
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function options()
    {
        return $this->belongsToMany(Option::class);
    }

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class, 'product_purchase')
            ->withPivot(['price', 'quantity', 'subtotal'])
            ->withTimestamps();
    }

    protected function wholesale(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function ($value) {
            $data = json_decode((string) $value, true) ?? [];
            if (empty($data) && $this->parent_id) {
                return $this->parent->wholesale;
            }

            return [
                'quantity' => array_keys($data),
                'price' => array_values($data),
            ];
        }, set: function ($value) {
            $data = [];
            foreach (($value['quantity'] ?? []) as $key => $quantity) {
                $data[$quantity] = $value['price'][$key];
            }
            ksort($data);

            return ['wholesale' => json_encode($data)];
        });
    }

    public function getPrice(int $quantity)
    {
        $wholesale = $this->wholesale;
        $price = $this->selling_price;

        foreach ($wholesale['quantity'] as $key => $value) {
            if ($quantity >= $value) {
                $price = $wholesale['price'][$key];
            }
        }

        return $price;
    }

    public function retailPrice(): int
    {
        $price = $this->suggested_price;

        if (is_string($price) && preg_match('/^\s*(\d+(?:\.\d+)?)\s*-\s*(\d+(?:\.\d+)?)\s*$/', $price, $matches)) {
            $low = (float) $matches[1];
            $high = (float) $matches[2];

            return (int) round(($low + $high) / 2);
        }

        if (is_numeric($price) && $price > 0) {
            return (int) round($price);
        }

        return (int) round($this->selling_price * 1.4);
    }

    public function suggestedRetailPrice(): string
    {
        if ($this->suggested_price) {
            return '৳'.$this->suggested_price;
        }

        return sprintf('৳%d - ৳%d', round($this->selling_price * 1.3), round($this->selling_price * 1.5));
    }

    protected function baseImage(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            $images = $this->images ?? collect();
            if ($images->isEmpty()) {
                $images = $this->parent->images ?? collect();
            }

            return $images->first(fn (Image $image): bool => $image->pivot->img_type == 'base');
        });
    }

    protected function additionalImages(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            $images = $this->images ?? collect();
            if ($images->isEmpty()) {
                $images = $this->parent->images ?? collect();
            }

            return $images->filter(fn (Image $image): bool => $image->pivot->img_type == 'additional');
        });
    }

    public function landings(): HasMany
    {
        return $this->hasMany(Landing::class);
    }

    /**
     * Get the indexable data array for the model.
     */
    public function toSearchableArray(): array
    {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
        ];
    }

    /**
     * Retrieve the model for route model binding.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
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

    public function shouldBeSearchable()
    {
        return $this->is_active;
    }

    public static function stockStatistics(): array
    {
        $products = static::where('should_track', true)->where('stock_count', '>', 0)->get([
            'id', 'stock_count', 'average_purchase_price', 'selling_price',
        ]);
        $totalStockCount = $products->sum('stock_count');
        $totalPurchaseValue = $products->sum(fn ($product): int|float => $product->stock_count * ($product->average_purchase_price ?? $product->selling_price));
        $totalSellValue = $products->sum(fn ($product): int|float => $product->stock_count * $product->selling_price);

        return [
            'totalStockCount' => $totalStockCount,
            'totalPurchaseValue' => theMoney($totalPurchaseValue),
            'totalSellValue' => theMoney($totalSellValue),
        ];
    }

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'hot_sale' => 'boolean',
            'new_arrival' => 'boolean',
            'should_track' => 'boolean',
        ];
    }

    /**
     * Override the reviews relationship to use our custom Review model.
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(\App\Models\Review::class, 'reviewable');
    }
}
