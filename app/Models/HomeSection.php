<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Laravel\SEO\Support\HasSEO;

class HomeSection extends Model
{
    use HasSEO;

    protected $fillable = [
        'title', 'type', 'items', 'order', 'data',
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
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function products($paginate = 0, $category = null)
    {
        $ids = $this->items ?? [];
        $rows = $this->data->rows ?? 3;
        $cols = $this->data->cols ?? 5;
        $sorted = setting('show_option')->product_sort ?? 'random';

        if ($this->type == 'carousel-grid') {
            $rows *= $cols;
        }

        // Optimize: Select only essential fields for better performance
        $query = Product::select([
            'id', 'name', 'slug', 'price', 'selling_price',
            'should_track', 'stock_count', 'is_active', 'parent_id', 'updated_at',
        ])
            ->whereIsActive(1)
            ->whereNull('parent_id');

        if ($category) {
            $query->whereHas('categories', function ($query) use ($category): void {
                $query->where('categories.id', $category);
            });
        } elseif (($this->data->source ?? false) == 'specific') {
            // Eager load categories to avoid N+1 queries
            $categoryIds = $this->categories()->pluck('categories.id')->toArray();
            $query->whereHas('categories', function ($query) use ($categoryIds): void {
                $query->whereIn('categories.id', $categoryIds);
            })
                ->orWhereIn('id', $ids);
        }

        $query->when(! $paginate, function ($query) use ($rows, $cols): void {
            $query->take($rows * $cols);
        });

        // Optimize: Use more efficient ordering
        $query->orderByRaw('(new_arrival = 1 OR hot_sale = 1) DESC');

        if ($ids) {
            if ($sorted == 'random') {
                $query->orderByRaw('CASE WHEN id IN ('.implode(',', $ids).') THEN 0 ELSE RAND()*(10-1)+1 END');
            } elseif ($sorted == 'updated_at') {
                $query->orderByRaw('CASE WHEN id IN ('.implode(',', $ids).') THEN 2038 ELSE updated_at END DESC');
            } elseif ($sorted == 'selling_price') {
                $query->orderByRaw('CASE WHEN id IN ('.implode(',', $ids).') THEN 0 ELSE selling_price END');
            }
        } else {
            if ($sorted == 'random') {
                $query->inRandomOrder();
            } elseif ($sorted == 'updated_at') {
                $query->latest('updated_at');
            } elseif ($sorted == 'selling_price') {
                $query->orderBy('selling_price');
            }
        }

        return $paginate
            ? $query->paginate($paginate)
            : $query->get();
    }

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'data' => 'object',
        ];
    }
}
