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
        if (config('cache.response_cache.enabled')) {
            ResponseCache::clear();
        }

        $this->clearRelatedCaches($model);
    }

    private function clearRelatedCaches(mixed $model): void
    {
        cacheInvalidateNamespace('api_sections');

        if ($model instanceof Category || $model instanceof Product) {
            cacheInvalidateNamespace('product_filters');
            cacheInvalidateNamespace('api_categories');
            cacheInvalidateNamespace('api_category');
            cacheInvalidateNamespace('related_products');
        }
    }
}
