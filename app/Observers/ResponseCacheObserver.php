<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Category;
use App\Models\Product;
use Spatie\ResponseCache\Facades\ResponseCache;

final class ResponseCacheObserver
{
    public function created(mixed $model): void
    {
        $this->clear($model);
    }

    public function updated(mixed $model): void
    {
        $this->clear($model);
    }

    public function deleted(mixed $model): void
    {
        $this->clear($model);
    }

    public function restored(mixed $model): void
    {
        $this->clear($model);
    }

    public function forceDeleted(mixed $model): void
    {
        $this->clear($model);
    }

    private function clear(mixed $model): void
    {
        if (! config('cache.response_cache.enabled')) {
            return;
        }

        ResponseCache::clear();

        // Clear related caches
        $this->clearRelatedCaches($model);
    }

    private function clearRelatedCaches(mixed $model): void
    {
        // Clear product filter data cache (general)
        cacheMemo()->forget('product_filter_data');

        // Clear API caches
        cacheMemo()->forget('api_categories:all');
        cacheMemo()->forget('api_sections');

        // Clear nested category caches (we clear a few common ones)
        for ($i = 0; $i <= 5; $i++) {
            cacheMemo()->forget("api_categories:nested:{$i}");
        }

        if ($model instanceof Category) {
            $this->forgetCategoryCaches($model);
        }

        if ($model instanceof Product) {
            $this->forgetProductCaches($model);
        }
    }

    private function forgetCategoryCaches(Category $category): void
    {
        cacheMemo()->forget('product_filter_data:category:'.$category->getKey());

        if ($category->slug) {
            cacheMemo()->forget('api_category:'.$category->slug);
        }

        $productIds = $category->products()
            ->whereNull('parent_id')
            ->pluck('products.id')
            ->all();

        $this->forgetRelatedProductsByIds($productIds);
    }

    private function forgetProductCaches(Product $product): void
    {
        cacheMemo()->forget('related_products:'.$product->getKey());

        $categoryIds = $product->categories()
            ->pluck('categories.id')
            ->all();

        foreach (array_unique($categoryIds) as $categoryId) {
            cacheMemo()->forget('product_filter_data:category:'.$categoryId);
        }

        if (! empty($categoryIds)) {
            $relatedProductIds = Product::whereHas('categories', function ($query) use ($categoryIds): void {
                $query->whereIn('categories.id', $categoryIds);
            })
                ->whereNull('parent_id')
                ->pluck('products.id')
                ->all();

            $this->forgetRelatedProductsByIds($relatedProductIds);
        }
    }

    private function forgetRelatedProductsByIds(array $productIds): void
    {
        foreach (array_unique($productIds) as $id) {
            cacheMemo()->forget('related_products:'.$id);
        }
    }
}
