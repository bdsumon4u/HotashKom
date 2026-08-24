<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class HomeSection extends Model
{
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
            $ids = $this->items ?? [];
            $rows = $this->data->rows ?? 3;
            $cols = $this->data->cols ?? 5;
            $sorted = setting('show_option')->product_sort ?? 'random';

            // A fixed seed keeps random-looking pagination stable across requests.
            $randomSeed = crc32('home-section-'.$this->id) & 0x7FFFFFFF;

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
            }

            $query->orderByRaw('(new_arrival = 1 OR hot_sale = 1) DESC');

            if ($ids) {
                if ($sorted == 'random') {
                    $query->orderByRaw(
                        'CASE WHEN id IN ('.implode(',', $ids).') THEN 0 ELSE RAND('.$randomSeed.') END'
                    );
                } elseif ($sorted == 'updated_at') {
                    $query->orderByRaw('CASE WHEN id IN ('.implode(',', $ids).') THEN 2038 ELSE updated_at END DESC');
                } elseif ($sorted == 'created_at') {
                    $query->orderByRaw('CASE WHEN id IN ('.implode(',', $ids).') THEN 2038 ELSE created_at END DESC');
                } elseif ($sorted == 'selling_price') {
                    $query->orderByRaw('CASE WHEN id IN ('.implode(',', $ids).') THEN 0 ELSE selling_price END');
                }
            } else {
                if ($sorted == 'random') {
                    $query->inRandomOrder($randomSeed);
                } elseif ($sorted == 'updated_at') {
                    $query->latest('updated_at');
                } elseif ($sorted == 'created_at') {
                    $query->latest('created_at');
                } elseif ($sorted == 'selling_price') {
                    $query->orderBy('selling_price');
                }
            }

            // Deterministic tie-breaker prevents records moving between pages.
            $query->orderBy('products.id');

            return $query->with([
                'reviews' => function ($q): void {
                    $q->where('approved', true)->with('ratings');
                },
            ])->withCount('variations')->paginate($paginate);
        }

        return cacheRememberNamespaced('section_products', 'section:'.$this->id, now()->addHours(2), function () {
            $ids = $this->items ?? [];
            $rows = $this->data->rows ?? 3;
            $cols = $this->data->cols ?? 5;
            $sorted = setting('show_option')->product_sort ?? 'random';

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
            // Pinned products remain prioritised only when they belong to one
            // of the categories selected for this section.
            $categoryIds = $this->categories()->pluck('categories.id')->map(
                static fn ($id): int => (int) $id
            )->all();

            if ($categoryIds) {
                $query->whereHas('categories', function ($query) use ($categoryIds): void {
                    $query->whereIn('categories.id', $categoryIds);
                });
            }

            $query->take($rows * $cols);

            $query->orderByRaw('(new_arrival = 1 OR hot_sale = 1) DESC');

            if ($ids) {
                if ($sorted == 'random') {
                    $query->orderByRaw('CASE WHEN id IN ('.implode(',', $ids).') THEN 0 ELSE RAND()*(10-1)+1 END');
                } elseif ($sorted == 'updated_at') {
                    $query->orderByRaw('CASE WHEN id IN ('.implode(',', $ids).') THEN 2038 ELSE updated_at END DESC');
                } elseif ($sorted == 'created_at') {
                    $query->orderByRaw('CASE WHEN id IN ('.implode(',', $ids).') THEN 2038 ELSE created_at END DESC');
                } elseif ($sorted == 'selling_price') {
                    $query->orderByRaw('CASE WHEN id IN ('.implode(',', $ids).') THEN 0 ELSE selling_price END');
                }
            } else {
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
