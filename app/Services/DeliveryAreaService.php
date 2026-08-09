<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DeliveryAreaService
{
    public function getDeliveryAreas(): Collection
    {
        $rawAreas = setting('delivery_areas');
        $areas = collect($rawAreas ?? [])
            ->map(function ($area): array {
                return [
                    'name' => (string) data_get($area, 'name', ''),
                    'cost' => (int) data_get($area, 'cost', 0),
                    'is_default' => (bool) data_get($area, 'is_default', false),
                ];
            })
            ->filter(fn (array $area): bool => filled($area['name']))
            ->values();

        if ($areas->isEmpty()) {
            $deliveryCharge = setting('delivery_charge');
            $areas = collect([
                [
                    'name' => 'Inside Dhaka',
                    'cost' => (int) data_get($deliveryCharge, 'inside_dhaka', 70),
                    'is_default' => true,
                ],
                [
                    'name' => 'Outside Dhaka',
                    'cost' => (int) data_get($deliveryCharge, 'outside_dhaka', 120),
                    'is_default' => false,
                ],
            ]);
        }

        return $areas;
    }

    public function getDefaultAreaName(): string
    {
        $areas = $this->getDeliveryAreas();

        return $areas->firstWhere('is_default', true)['name'] ?? '';
    }

    public function getInsideArea(?Collection $areas = null): ?array
    {
        $areas ??= $this->getDeliveryAreas();

        return $areas->first(function (array $area): bool {
            $lower = Str::lower($area['name']);

            return Str::contains($lower, 'inside') ||
                Str::contains($lower, 'ঢাকা শহর') ||
                Str::contains($lower, 'ঢাকা সিটি');
        }) ?? $areas->first();
    }

    public function getOutsideArea(?Collection $areas = null): ?array
    {
        $areas ??= $this->getDeliveryAreas();

        return $areas->first(function (array $area): bool {
            $lower = Str::lower($area['name']);

            return Str::contains($lower, 'outside') ||
                Str::contains($lower, 'বাহির');
        });
    }

    public function getProductDeliveryCharges(?Product $product = null): Collection
    {
        $areas = $this->getDeliveryAreas();
        $isProductWise = (bool) (setting('show_option')->productwise_delivery_charge ?? false);
        $insideArea = $this->getInsideArea($areas);
        $outsideArea = $this->getOutsideArea($areas);

        return $areas->map(function (array $area) use ($isProductWise, $product, $insideArea, $outsideArea): array {
            $cost = $area['cost'];

            if ($isProductWise && $product) {
                $areaName = $area['name'];
                $customCharges = $product->delivery_charges ?? [];

                if (is_array($customCharges) && array_key_exists($areaName, $customCharges) && ! is_null($customCharges[$areaName]) && $customCharges[$areaName] !== '') {
                    $cost = (int) $customCharges[$areaName];
                } elseif ($insideArea && $areaName === $insideArea['name'] && ! is_null($product->shipping_inside)) {
                    $cost = (int) $product->shipping_inside;
                } elseif ($outsideArea && $areaName === $outsideArea['name'] && ! is_null($product->shipping_outside)) {
                    $cost = (int) $product->shipping_outside;
                }
            }

            return [
                'name' => $area['name'],
                'cost' => $cost,
                'is_default' => $area['is_default'],
            ];
        });
    }

    public function calculateShippingCost(
        ?string $areaName,
        iterable $items,
        float $subtotal,
        ?bool &$isFreeDelivery = null
    ): int|float {
        $isFreeDelivery = false;

        if (! $areaName) {
            return 0;
        }

        $areas = $this->getDeliveryAreas();

        $normalizedAreaName = match ($areaName) {
            'inside' => 'Inside Dhaka',
            'outside' => 'Outside Dhaka',
            default => $areaName,
        };

        $areaSetting = $areas->first(fn (array $a): bool => $a['name'] === $normalizedAreaName);
        $areaCost = (int) data_get($areaSetting, 'cost', 0);

        $itemsCollection = collect($items);
        $showOption = setting('show_option');
        $isProductWise = (bool) ($showOption->productwise_delivery_charge ?? false);
        $isQuantityWise = (bool) ($showOption->quantitywise_delivery_charge ?? false);

        $shippingCost = 0;

        if ($isProductWise) {
            $insideArea = $this->getInsideArea($areas);
            $outsideArea = $this->getOutsideArea($areas);

            $resolveItemFee = function ($item) use ($normalizedAreaName, $insideArea, $outsideArea): int {
                $itemArray = is_array($item) ? $item : (array) $item;
                $options = data_get($item, 'options');
                $deliveryCharges = data_get($options, 'delivery_charges') ?? data_get($itemArray, 'delivery_charges', []);

                if (is_array($deliveryCharges) && array_key_exists($normalizedAreaName, $deliveryCharges) && ! is_null($deliveryCharges[$normalizedAreaName]) && $deliveryCharges[$normalizedAreaName] !== '') {
                    return (int) $deliveryCharges[$normalizedAreaName];
                }

                $isInside = ($insideArea && $normalizedAreaName === $insideArea['name']);
                $isOutside = ($outsideArea && $normalizedAreaName === $outsideArea['name']);

                if ($isInside) {
                    return (int) (data_get($options, 'shipping_inside') ?? data_get($itemArray, 'shipping_inside', 0));
                }

                if ($isOutside) {
                    return (int) (data_get($options, 'shipping_outside') ?? data_get($itemArray, 'shipping_outside', 0));
                }

                return 0;
            };

            if ($isQuantityWise) {
                $shippingCost = $itemsCollection->sum(function ($item) use ($resolveItemFee): int|float {
                    $qty = (int) (data_get($item, 'qty') ?? data_get($item, 'quantity', 1));

                    return $resolveItemFee($item) * $qty;
                });
            } else {
                $shippingCost = $itemsCollection->max(function ($item) use ($resolveItemFee): int|float {
                    return $resolveItemFee($item);
                });
            }

            $shippingCost = $shippingCost ?: $areaCost;
        } else {
            $shippingCost = $areaCost;
        }

        $freeDelivery = setting('free_delivery');

        if (! ((bool) ($freeDelivery->enabled ?? false)) || ($freeDelivery->enabled ?? false) == 'false') {
            return $shippingCost;
        }

        if ($freeDelivery->for_all ?? false) {
            if ($subtotal < $freeDelivery->min_amount) {
                return $shippingCost;
            }

            $totalQty = $itemsCollection->sum(function ($item): int {
                return (int) (data_get($item, 'qty') ?? data_get($item, 'quantity', 0));
            });

            if ($totalQty < $freeDelivery->min_quantity) {
                return $shippingCost;
            }

            $isFreeDelivery = true;

            return 0;
        }

        $freeProducts = (array) ($freeDelivery->products ?? []);
        foreach ($freeProducts as $parentId => $minQty) {
            $matched = $itemsCollection->contains(function ($item) use ($parentId, $minQty): bool {
                $itemParentId = data_get($item, 'options.parent_id') ?? data_get($item, 'parent_id');
                $itemQty = data_get($item, 'qty') ?? data_get($item, 'quantity', 0);

                return (string) $itemParentId === (string) $parentId && (int) $itemQty >= (int) $minQty;
            });

            if ($matched) {
                $isFreeDelivery = true;

                return 0;
            }
        }

        return $shippingCost;
    }
}
