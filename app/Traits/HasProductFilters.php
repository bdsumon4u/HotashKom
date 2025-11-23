<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Filter by attributes/options
        if ($request->filter_option) {
            $optionIds = is_array($request->filter_option)
                ? $request->filter_option
                : explode(',', $request->filter_option);
            $optionIds = array_filter($optionIds);

            if (! empty($optionIds)) {
                // Filter products that have the selected options either directly or through variations
                $query->where(function ($q) use ($optionIds): void {
                    // Products that have the options directly
                    $q->whereHas('options', function ($optQuery) use ($optionIds): void {
                        $optQuery->whereIn('options.id', $optionIds);
                    })
                        // OR products that have variations with the selected options
                        ->orWhereHas('variations', function ($varQuery) use ($optionIds): void {
                            $varQuery->whereHas('options', function ($optQuery) use ($optionIds): void {
                                $optQuery->whereIn('options.id', $optionIds);
                            });
                        });
                });
            }
        }
    }

    /**
     * Apply product sorting to a query.
     */
    protected function applyProductSorting(Builder|Relation $query): void
    {
        $sorted = setting('show_option')->product_sort ?? 'random';

        $query->orderByRaw('(new_arrival = 1 OR hot_sale = 1) DESC');

        if ($sorted == 'random') {
            $seed = request()->get('shuffle');
            if (! $seed) {
                $seed = (string) random_int(1, PHP_INT_MAX);
                request()->merge(['shuffle' => $seed]);
            }

            $seedInt = abs((int) crc32((string) $seed));
            $driver = DB::connection()->getDriverName();

            if ($driver === 'mysql') {
                $query->orderByRaw('RAND('.$seedInt.')');
            } elseif ($driver === 'pgsql') {
                $query->orderByRaw('RANDOM()');
            } else {
                $query->inRandomOrder();
            }
        } elseif ($sorted == 'updated_at') {
            $query->latest('updated_at');
        } elseif ($sorted == 'selling_price') {
            $query->orderBy('selling_price');
        }
    }

    /**
     * Get filter data for products (categories, attributes with options).
     *
     * @param  \App\Models\Category|null  $category  Optional category to filter attributes by
     * @return array{categories: \Illuminate\Database\Eloquent\Collection, attributes: \Illuminate\Database\Eloquent\Collection}
     */
    protected function getProductFilterData(?Category $category = null): array
    {
        $cacheKey = 'filters'.($category ? ':category:'.$category->id : '');

        // Use flexible caching: 2 hours fresh, 4 hours stale (total 6 hours)
        // Fresh: Data is served immediately from cache
        // Stale: Data is served from cache while background revalidation happens
        return cacheFlexibleNamespaced('product_filters', $cacheKey, [
            'fresh' => 7200, // 2 hours in seconds
            'stale' => 14400, // 4 hours in seconds (total 6 hours)
        ], function () use ($category): array {
            // Optimize: Pre-fetch active product IDs to avoid repeated queries
            $activeProductIds = Product::whereIsActive(1)
                ->whereNull('parent_id')
                ->pluck('id')
                ->toArray();

            if (empty($activeProductIds)) {
                return [
                    'categories' => collect(),
                    'attributes' => collect(),
                ];
            }

            // Get categories that have products - optimized with pre-fetched IDs
            $categories = Category::nested(0, true)
                ->filter(function ($category) use ($activeProductIds) {
                    // Use whereHas with pre-filtered product IDs for better performance
                    $hasProducts = $category->products()
                        ->whereIn('products.id', $activeProductIds)
                        ->exists();

                    $hasChildProducts = $category->childrens->some(function ($child) use ($activeProductIds) {
                        return $child->products()
                            ->whereIn('products.id', $activeProductIds)
                            ->exists();
                    });

                    return $hasProducts || $hasChildProducts;
                })
                ->map(function ($category) use ($activeProductIds) {
                    $category->setRelation('childrens', $category->childrens->filter(function ($child) use ($activeProductIds) {
                        return $child->products()
                            ->whereIn('products.id', $activeProductIds)
                            ->exists();
                    }));

                    return $category;
                })
                ->values();

            // Get attributes that have options used in active products
            // Optimized: Pre-fetch product IDs and use direct joins instead of nested whereHas
            $attributesQuery = Attribute::query();

            // If category is provided, get product IDs for that category first
            $categoryProductIds = $activeProductIds;
            if ($category) {
                $categoryProductIds = DB::table('category_product')
                    ->where('category_id', $category->id)
                    ->whereIn('product_id', $activeProductIds)
                    ->pluck('product_id')
                    ->toArray();
            }

            // Get variation product IDs (products with parent_id) whose parents are active
            $variationProductIds = Product::whereIn('parent_id', $activeProductIds)
                ->pluck('id')
                ->toArray();

            // Get all product IDs (parent + variations) that should be considered
            $allProductIds = array_merge($activeProductIds, $variationProductIds);
            if ($category && ! empty($categoryProductIds)) {
                $allProductIds = array_intersect($allProductIds, array_merge($categoryProductIds, $variationProductIds));
            }

            // Optimized: Use whereHas with pre-filtered product IDs
            $attributesQuery->whereHas('options', function ($query) use ($allProductIds, $categoryProductIds, $activeProductIds, $variationProductIds, $category): void {
                $query->whereHas('products', function ($prodQuery) use ($allProductIds, $categoryProductIds, $activeProductIds, $variationProductIds, $category): void {
                    $prodQuery->whereIn('products.id', $allProductIds)
                        ->where(function ($q) use ($categoryProductIds, $activeProductIds, $variationProductIds, $category): void {
                            // Options linked directly to parent products
                            $q->where(function ($parentQ) use ($categoryProductIds, $activeProductIds, $category): void {
                                $parentQ->whereIn('products.id', $category ? $categoryProductIds : $activeProductIds)
                                    ->whereNull('products.parent_id');
                            })
                                // OR options linked to variations
                                ->orWhere(function ($varQ) use ($variationProductIds, $activeProductIds, $categoryProductIds, $category): void {
                                    $varQ->whereIn('products.id', $variationProductIds)
                                        ->whereIn('products.parent_id', $category ? $categoryProductIds : $activeProductIds);
                                });
                        });
                });
            })
                ->with(['options' => function ($query) use ($allProductIds, $categoryProductIds, $activeProductIds, $variationProductIds, $category): void {
                    $query->whereHas('products', function ($prodQuery) use ($allProductIds, $categoryProductIds, $activeProductIds, $variationProductIds, $category): void {
                        $prodQuery->whereIn('products.id', $allProductIds)
                            ->where(function ($q) use ($categoryProductIds, $activeProductIds, $variationProductIds, $category): void {
                                $q->where(function ($parentQ) use ($categoryProductIds, $activeProductIds, $category): void {
                                    $parentQ->whereIn('products.id', $category ? $categoryProductIds : $activeProductIds)
                                        ->whereNull('products.parent_id');
                                })
                                    ->orWhere(function ($varQ) use ($variationProductIds, $activeProductIds, $categoryProductIds, $category): void {
                                        $varQ->whereIn('products.id', $variationProductIds)
                                            ->whereIn('products.parent_id', $category ? $categoryProductIds : $activeProductIds);
                                    });
                            });
                    });
                }]);

            $attributes = $attributesQuery->get()
                ->filter(function ($attribute) {
                    // Only include attributes that have at least one option with products
                    return $attribute->options->isNotEmpty();
                })
                ->values();

            return [
                'categories' => $categories,
                'attributes' => $attributes,
            ];
        });
    }
}
