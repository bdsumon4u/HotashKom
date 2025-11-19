<?php

declare(strict_types=1);

namespace App\Observers;

use Spatie\ResponseCache\Facades\ResponseCache;

final class ResponseCacheObserver
{
    public function created(mixed $model): void
    {
        $this->clear();
    }

    public function updated(mixed $model): void
    {
        $this->clear();
    }

    public function deleted(mixed $model): void
    {
        $this->clear();
    }

    public function restored(mixed $model): void
    {
        $this->clear();
    }

    public function forceDeleted(mixed $model): void
    {
        $this->clear();
    }

    private function clear(): void
    {
        if (! config('cache.response_cache.enabled')) {
            return;
        }

        ResponseCache::clear();

        // Clear related caches
        $this->clearRelatedCaches();
    }

    private function clearRelatedCaches(): void
    {
        // Clear product filter data cache (general and category-specific)
        cacheMemo()->forget('product_filter_data');
        // Note: Category-specific cache keys will be cleared when categories are updated

        // Clear API caches
        cacheMemo()->forget('api_categories:all');
        cacheMemo()->forget('api_sections');

        // Clear nested category caches (we clear a few common ones)
        for ($i = 0; $i <= 5; $i++) {
            cacheMemo()->forget("api_categories:nested:{$i}");
        }
    }
}
