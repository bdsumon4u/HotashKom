<?php

namespace App\Services;

use App\Jobs\SyncProductStockWithResellers;
use App\Models\Order;
use App\Models\Product;
use App\Observers\ResponseCacheObserver;
use Illuminate\Validation\ValidationException;

class ProductStockService
{
    public function assertOrderStock(Order $order): void
    {
        if (Product::allowsOverselling()) {
            return;
        }

        $decrementStatuses = config('app.decrement', []);
        $stockAlreadyDeducted = ! config('accounting.enabled', false) && in_array($order->getOriginal('status'), $decrementStatuses, true);
        $consumingStock = ! config('accounting.enabled', false) && $order->isDirty('status')
            && in_array($order->status, ['CONFIRMED', 'PACKAGING', 'SHIPPING'], true)
            && in_array($order->status, $decrementStatuses, true)
            && ! in_array($order->getOriginal('status'), $decrementStatuses, true);

        if ($order->exists && ! $order->isDirty('products') && ! $consumingStock) {
            return;
        }

        $originalProducts = $order->exists ? (json_decode((string) $order->getRawOriginal('products'), true) ?: []) : [];
        $required = [];

        foreach ((array) $order->products as $id => $item) {
            $quantity = (int) data_get($item, 'quantity', 0);
            $previousQuantity = $order->exists && ! $consumingStock ? (int) data_get($originalProducts, $id.'.quantity', 0) : 0;
            $increase = max(0, $quantity - $previousQuantity);
            $required[(int) $id] = $increase > 0 && (! $order->exists || ! $stockAlreadyDeducted || $consumingStock) ? $quantity : $increase;
        }

        $required = array_filter($required);

        if ($required === []) {
            return;
        }

        $products = Product::query()->withoutGlobalScope('latest')
            ->whereKey(array_keys($required))->orderBy('id')->lockForUpdate()->get();

        if ($products->count() !== count($required)) {
            throw ValidationException::withMessages([
                'products' => 'Some selected products are no longer available. Refresh the cart and try again.',
            ]);
        }

        foreach ($products as $product) {
            if (! $product->canFulfillQuantity($required[$product->id])) {
                throw ValidationException::withMessages([
                    'products' => $product->name.' has insufficient stock. Available: '.max(0, (int) $product->stock_count).'.',
                ]);
            }
        }
    }

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
