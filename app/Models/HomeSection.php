<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class HomeSection extends Model
{
    use BelongsToTenant;
    use HasSEO;

    protected $fillable = [
        'title', 'type', 'items', 'order', 'data', 'content',
    ];

    protected $with = ['categories'];

    #[\Override]
    public static function booted(): void
    {
        static::created(function (): void {
            static::clearHomeSectionCaches();
        });

        static::saved(function (): void {
            static::clearHomeSectionCaches();
        });

        static::deleted(function (): void {
            static::clearHomeSectionCaches();
        });
    }

    /**
     * Clear all home section-related caches.
     */
    private static function clearHomeSectionCaches(): void
    {
        // Clear home sections cache
        cacheMemo()->forget('homesections');

        // Clear API sections cache (both direct and namespaced)
        cacheMemo()->forget('api_sections');
        cacheInvalidateNamespace('api_sections');

        // Clear product filter data since sections affect product listings
        cacheMemo()->forget('product_filter_data');

        // Clear related namespaced caches
        cacheInvalidateNamespace('product_filters');
        cacheInvalidateNamespace('section_products');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function products($paginate = 0, $category = null)
    {
        if ($paginate || $category) {
            $ids = array_values(array_filter(array_map('intval', (array) ($this->items ?? []))));
            $rows = (int) ($this->data->rows ?? 3);
            $cols = (int) ($this->data->cols ?? 5);
            $source = $this->data->source ?? null;
            $sorted = setting('show_option')->product_sort ?? 'random';
            $isSpecific = $source === 'specific' || ($source !== 'available' && ($category || $this->categories->isNotEmpty() || ! empty($ids)));

            if ($this->type == 'carousel-grid') {
                $rows *= $cols;
            }

            $query = Product::select([
                'id', 'name', 'slug', 'price', 'selling_price', 'suggested_price',
                'should_track', 'stock_count', 'is_active', 'parent_id', 'updated_at',
            ])
                ->whereIsActive(1)
                ->whereNull('parent_id');

            // Strict category filtering for category-based visual sections.
            // Products manually pinned in $items can be prioritised, but cannot
            // bypass the section's selected category/category-child rules.
            $categoryIds = $category
                ? [(int) $category]
                : $this->categories()->pluck('categories.id')->map(
                    static fn ($id): int => (int) $id
                )->all();

            if ($categoryIds) {
                $query->whereHas('categories', function ($query) use ($categoryIds): void {
                    $query->whereIn('categories.id', $categoryIds);
                });
            } elseif ($isSpecific) {
                if (! empty($ids)) {
                    $query->whereIn('products.id', $ids);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }

            if ($ids) {
                $caseOrder = 'CASE products.id '.implode(' ', array_map(fn ($id, $i) => "WHEN {$id} THEN {$i}", $ids, range(1, count($ids)))).' ELSE 999999 END';

                if ($sorted == 'random') {
                    $query->orderByRaw("{$caseOrder} ASC, (new_arrival = 1 OR hot_sale = 1) DESC, RAND()*(10-1)+1");
                } elseif ($sorted == 'updated_at') {
                    $query->orderByRaw("{$caseOrder} ASC, (new_arrival = 1 OR hot_sale = 1) DESC, products.updated_at DESC");
                } elseif ($sorted == 'created_at') {
                    $query->orderByRaw("{$caseOrder} ASC, (new_arrival = 1 OR hot_sale = 1) DESC, products.created_at DESC");
                } elseif ($sorted == 'selling_price') {
                    $query->orderByRaw("{$caseOrder} ASC, (new_arrival = 1 OR hot_sale = 1) DESC, products.selling_price ASC");
                } else {
                    $query->orderByRaw("{$caseOrder} ASC, (new_arrival = 1 OR hot_sale = 1) DESC");
                }
            } else {
                $query->orderByRaw('(new_arrival = 1 OR hot_sale = 1) DESC');

                if ($sorted == 'random') {
                    $query->inRandomOrder();
                } elseif ($sorted == 'updated_at') {
                    $query->latest('updated_at');
                } elseif ($sorted == 'created_at') {
                    $query->latest('created_at');
                } elseif ($sorted == 'selling_price') {
                    $query->orderBy('selling_price');
                }
            }

            return $query->with([
                'reviews' => function ($q): void {
                    $q->where('approved', true)->with('ratings');
                },
            ])->withCount('variations')->paginate($paginate);
        }

        return cacheRememberNamespaced('section_products', 'section:'.$this->id, now()->addHours(2), function () {
            $ids = array_values(array_filter(array_map('intval', (array) ($this->items ?? []))));
            $rows = (int) ($this->data->rows ?? 3);
            $cols = (int) ($this->data->cols ?? 5);
            $source = $this->data->source ?? null;
            $sorted = setting('show_option')->product_sort ?? 'random';
            $isSpecific = $source === 'specific' || ($source !== 'available' && ($this->categories->isNotEmpty() || ! empty($ids)));

            if ($this->type == 'carousel-grid') {
                $rows *= $cols;
            }

            $query = Product::select([
                'id', 'name', 'slug', 'price', 'selling_price', 'suggested_price',
                'should_track', 'stock_count', 'is_active', 'parent_id', 'updated_at',
            ])
                ->whereIsActive(1)
                ->whereNull('parent_id');

            $categoryIds = $this->categories()->pluck('categories.id')->map(
                static fn ($id): int => (int) $id
            )->all();

            if ($categoryIds) {
                $query->whereHas('categories', function ($query) use ($categoryIds): void {
                    $query->whereIn('categories.id', $categoryIds);
                });
            } elseif ($isSpecific) {
                if (! empty($ids)) {
                    $query->whereIn('products.id', $ids);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }

            $query->take($rows * $cols);

            if ($ids) {
                $caseOrder = 'CASE products.id '.implode(' ', array_map(fn ($id, $i) => "WHEN {$id} THEN {$i}", $ids, range(1, count($ids)))).' ELSE 999999 END';

                if ($sorted == 'random') {
                    $query->orderByRaw("{$caseOrder} ASC, (new_arrival = 1 OR hot_sale = 1) DESC, RAND()*(10-1)+1");
                } elseif ($sorted == 'updated_at') {
                    $query->orderByRaw("{$caseOrder} ASC, (new_arrival = 1 OR hot_sale = 1) DESC, products.updated_at DESC");
                } elseif ($sorted == 'created_at') {
                    $query->orderByRaw("{$caseOrder} ASC, (new_arrival = 1 OR hot_sale = 1) DESC, products.created_at DESC");
                } elseif ($sorted == 'selling_price') {
                    $query->orderByRaw("{$caseOrder} ASC, (new_arrival = 1 OR hot_sale = 1) DESC, products.selling_price ASC");
                } else {
                    $query->orderByRaw("{$caseOrder} ASC, (new_arrival = 1 OR hot_sale = 1) DESC");
                }
            } else {
                $query->orderByRaw('(new_arrival = 1 OR hot_sale = 1) DESC');

                if ($sorted == 'random') {
                    $query->inRandomOrder();
                } elseif ($sorted == 'updated_at') {
                    $query->latest('updated_at');
                } elseif ($sorted == 'created_at') {
                    $query->latest('created_at');
                } elseif ($sorted == 'selling_price') {
                    $query->orderBy('selling_price');
                }
            }

            return $query->with([
                'reviews' => function ($q): void {
                    $q->where('approved', true)->with('ratings');
                },
            ])->withCount('variations')->get();
        });
    }

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'data' => 'object',
        ];
    }

    /**
     * Get dynamic SEO data fallback.
     */
    public function getDynamicSEOData(): SEOData
    {
        $title = $this->seo?->title ?: $this->title;
        $description = $this->seo?->description;
        $image = $this->seo?->image;

        return new SEOData(
            title: $title,
            description: $description,
            image: $image,
        );
    }
}
