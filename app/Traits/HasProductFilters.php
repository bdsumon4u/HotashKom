<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;

trait HasProductFilters
{
    /**
     * Apply product filters to a query.
     */
    protected function applyProductFilters(Builder|Relation $query, Request $request): void
    {
        // Filter by categories
        if ($request->filter_category) {
            $categoryFilter = $request->filter_category;
            if (is_array($categoryFilter)) {
                $query->whereHas('categories', function ($q) use ($categoryFilter): void {
                    $q->whereIn('categories.id', array_filter($categoryFilter));
                });
            } elseif (is_numeric(str_replace(',', '', $categoryFilter))) {
                $query->whereHas('categories', function ($q) use ($categoryFilter): void {
                    $q->whereIn('categories.id', explode(',', $categoryFilter));
                });
            } else {
                $query->whereHas('categories', function ($q) use ($categoryFilter): void {
                    $q->where('categories.slug', rawurldecode($categoryFilter));
                });
            }
        }

        // Filter by brands
        if ($request->filter_brand) {
            $brandIds = is_array($request->filter_brand)
                ? $request->filter_brand
                : explode(',', $request->filter_brand);
            $query->whereIn('brand_id', array_filter($brandIds));
        }

        // Filter by price range
        if ($request->min_price) {
            $query->where('selling_price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('selling_price', '<=', $request->max_price);
        }
    }

    /**
     * Apply product sorting to a query.
     */
    protected function applyProductSorting(Builder|Relation $query): void
    {
        $sorted = setting('show_option')->product_sort ?? 'random';
        if ($sorted == 'random') {
            $query->inRandomOrder();
        } elseif ($sorted == 'updated_at') {
            $query->latest('updated_at');
        } elseif ($sorted == 'selling_price') {
            $query->orderBy('selling_price');
        }
    }

    /**
     * Get filter data for products (categories, brands, price range).
     *
     * @return array{categories: \Illuminate\Database\Eloquent\Collection, brands: \Illuminate\Database\Eloquent\Collection, priceRange: \App\Models\Product|null}
     */
    protected function getProductFilterData(): array
    {
        // Get categories that have products
        $categories = Category::nested(0, true)
            ->filter(function ($category) {
                $hasProducts = $category->products()
                    ->whereIsActive(1)
                    ->whereNull('parent_id')
                    ->exists();

                $hasChildProducts = $category->childrens->some(function ($child) {
                    return $child->products()
                        ->whereIsActive(1)
                        ->whereNull('parent_id')
                        ->exists();
                });

                return $hasProducts || $hasChildProducts;
            })
            ->map(function ($category) {
                $category->setRelation('childrens', $category->childrens->filter(function ($child) {
                    return $child->products()
                        ->whereIsActive(1)
                        ->whereNull('parent_id')
                        ->exists();
                }));

                return $category;
            })
            ->values();

        // Get brands that have products
        $brands = Brand::cached()
            ->filter(function ($brand) {
                return $brand->products()
                    ->whereIsActive(1)
                    ->whereNull('parent_id')
                    ->exists();
            })
            ->values();

        // Get price range
        $priceRange = Product::whereIsActive(1)
            ->whereNull('parent_id')
            ->withoutGlobalScope('latest')
            ->selectRaw('MIN(selling_price) as min_price, MAX(selling_price) as max_price')
            ->first();

        return [
            'categories' => $categories,
            'brands' => $brands,
            'priceRange' => $priceRange,
        ];
    }
}
