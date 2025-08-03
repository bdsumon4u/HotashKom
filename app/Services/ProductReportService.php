<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Carbon;

final readonly class ProductReportService
{
    /**
     * Generate products report for orders with specific statuses
     */
    public function generateProductsReport(
        Carbon $startDate,
        Carbon $endDate,
        array $statuses,
        ?string $dateType = 'status_at',
        ?int $staffId = null,
        ?Carbon $shippedAt = null
    ): array {
        $orderQuery = Order::query()
            ->whereBetween($dateType, [
                $startDate->startOfDay()->toDateTimeString(),
                $endDate->endOfDay()->toDateTimeString(),
            ]);

        if ($staffId) {
            $orderQuery->where('admin_id', $staffId);
        }

        if ($shippedAt) {
            $orderQuery->whereNotNull('shipped_at')
                ->whereDate('shipped_at', $shippedAt);
        }

        $productInOrders = [];

        $products = (clone $orderQuery)->get()
            ->whereIn('status', $statuses)
            ->flatMap(function ($order) use (&$productInOrders) {
                $products = json_decode(json_encode($order->products, JSON_UNESCAPED_UNICODE), true);

                foreach ($products as $product) {
                    // Count unique orders for each product
                    $productInOrders[$product['name']][$order->id] = 1;
                }

                return $products;
            })
            ->groupBy('name') // Group by name instead of id to avoid duplicates
            ->mapWithKeys(fn ($item, $name) => [$name => [
                'name' => $name,
                'slug' => $item->first()['slug'] ?? '',
                'quantity' => $item->sum('quantity'),
                'total' => $item->sum('total'),
                'purchase_cost' => $item->sum(function ($product) {
                    return ((isset($product['purchase_price']) && $product['purchase_price']) ? $product['purchase_price'] : $product['price']) * $product['quantity'];
                }),
            ]])
            ->sortByDesc('quantity')
            ->toArray();

        return [
            'products' => $products,
            'productInOrders' => $productInOrders,
        ];
    }
}
