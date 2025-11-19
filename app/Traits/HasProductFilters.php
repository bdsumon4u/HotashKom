<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Attribute;
use App\Models\Category;
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
            $query->inRandomOrder();
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
        $cacheKey = 'product_filter_data'.($category ? ':category:'.$category->id : '');

        return cacheMemo()->remember($cacheKey, now()->addHour(), function () use ($category): array {
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

            // Get attributes that have options used in active products
            // Options are typically linked to variation products (with parent_id), not parent products
            // So we need to check if the variation's parent product is active and has no parent_id
            $attributesQuery = Attribute::whereHas('options', function ($query) use ($category): void {
                $query->whereHas('products', function ($prodQuery) use ($category): void {
                    $prodQuery->whereIsActive(1)
                        ->where(function ($q) use ($category): void {
                            // Options linked directly to parent products (no parent_id)
                            $q->where(function ($parentQ) use ($category): void {
                                $parentQ->whereNull('parent_id');

                                // If category is provided, filter by category
                                if ($category) {
                                    $parentQ->whereHas('categories', function ($catQuery) use ($category): void {
                                        $catQuery->where('categories.id', $category->id);
                                    });
                                }
                            })
                                // OR options linked to variations - check if parent is active
                                ->orWhereHas('parent', function ($parentQuery) use ($category): void {
                                    $parentQuery->whereIsActive(1)->whereNull('parent_id');

                                    // If category is provided, filter by category
                                    if ($category) {
                                        $parentQuery->whereHas('categories', function ($catQuery) use ($category): void {
                                            $catQuery->where('categories.id', $category->id);
                                        });
                                    }
                                });
                        });
                });
            })
                ->with(['options' => function ($query) use ($category): void {
                    $query->whereHas('products', function ($prodQuery) use ($category): void {
                        $prodQuery->whereIsActive(1)
                            ->where(function ($q) use ($category): void {
                                // Options linked directly to parent products (no parent_id)
                                $q->where(function ($parentQ) use ($category): void {
                                    $parentQ->whereNull('parent_id');

                                    // If category is provided, filter by category
                                    if ($category) {
                                        $parentQ->whereHas('categories', function ($catQuery) use ($category): void {
                                            $catQuery->where('categories.id', $category->id);
                                        });
                                    }
                                })
                                    // OR options linked to variations - check if parent is active
                                    ->orWhereHas('parent', function ($parentQuery) use ($category): void {
                                        $parentQuery->whereIsActive(1)->whereNull('parent_id');

                                        // If category is provided, filter by category
                                        if ($category) {
                                            $parentQuery->whereHas('categories', function ($catQuery) use ($category): void {
                                                $catQuery->where('categories.id', $category->id);
                                            });
                                        }
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
