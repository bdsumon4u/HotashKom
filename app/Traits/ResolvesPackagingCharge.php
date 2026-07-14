<?php

namespace App\Traits;

use App\Models\Product;

trait ResolvesPackagingCharge
{
    /**
     * Resolve the packaging charge for a reseller order.
     *
     * Uses the maximum packaging_charge across the given products.
     * Falls back to config('app.packaging_charge', 25) when no product has a charge set.
     */
    protected function resolvePackagingCharge(array $products): int
    {
        $default = (int) config('app.packaging_charge', 25);

        if (! config('app.resell')) {
            return $default;
        }

        $ids = [];
        foreach ($products as $key => $value) {
            if (is_numeric($key) && $key > 0) {
                $ids[] = (int) $key;
            }
            if (is_numeric($value) && $value > 0) {
                $ids[] = (int) $value;
            }
            if (is_array($value) && isset($value['id'])) {
                $ids[] = (int) $value['id'];
            }
            if (is_object($value) && isset($value->id)) {
                $ids[] = (int) $value->id;
            }
        }
        $ids = array_unique(array_filter($ids));

        if (empty($ids)) {
            return $default;
        }

        $dbProducts = Product::with('parent')->whereIn('id', $ids)->get();

        $charges = $dbProducts->map(function ($product) use ($default) {
            if ($product->packaging_charge !== null) {
                return (int) $product->packaging_charge;
            }

            if ($product->parent_id && $product->parent) {
                if ($product->parent->packaging_charge !== null) {
                    return (int) $product->parent->packaging_charge;
                }
            }

            return $default;
        });

        return $charges->isNotEmpty() ? $charges->max() : $default;
    }
}
