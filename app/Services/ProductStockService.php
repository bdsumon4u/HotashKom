<?php

namespace App\Services;

use App\Jobs\SyncProductStockWithResellers;
use App\Models\Product;
use App\Observers\ResponseCacheObserver;

class ProductStockService
{
    /**
     * @param  array<int, int>  $stockChanges  Product IDs mapped to signed quantity changes.
     */
    public function adjust(array $stockChanges, bool $enableTracking = false): void
    {
        $stockChanges = array_filter($stockChanges, fn (int $quantity): bool => $quantity !== 0);

        if ($stockChanges === []) {
            return;
        }

        $query = Product::query()->withoutGlobalScope('latest')->whereKey(array_keys($stockChanges));
        $connection = $query->getConnection();
        $cases = [];

        foreach ($stockChanges as $productId => $quantity) {
            $cases[] = sprintf('WHEN %d THEN %d', (int) $productId, $quantity);
        }

        $values = [
            'stock_count' => $connection->raw('stock_count + CASE id '.implode(' ', $cases).' ELSE 0 END'),
        ];

        if ($enableTracking) {
            $values['should_track'] = true;
        } else {
            $query->where('should_track', true);
        }

        if ($query->update($values) === 0) {
            return;
        }

        $connection->afterCommit(function () use ($stockChanges): void {
            $products = Product::query()
                ->with(['categories', 'parent.categories'])
                ->whereKey(array_keys($stockChanges))
                ->where('should_track', true)
                ->get();

            if ($products->isEmpty()) {
                return;
            }

            app(ResponseCacheObserver::class)->updated($products->first());

            foreach ($products as $product) {
                Product::clearProductCaches($product);
            }

            SyncProductStockWithResellers::dispatch($products->modelKeys());
        });
    }
}
