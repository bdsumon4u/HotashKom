<?php

namespace App\Services;

class LandingPageProComboPricing
{
    /**
     * @param  array<int, array{name: string, scope?: string, total_price: int, min_quantity?: int, requirements: array<int, array{product_id: int, min_quantity: int}>}>  $rules
     * @param  array<int, array{parent_id: int, quantity: int, price: int|float}>  $items
     * @return array{subtotal: float, applied_rules: array<int, array{name: string, applications: int, quantity: int, total: float}>}
     */
    public function quote(array $rules, array $items): array
    {
        $quantities = [];
        $catalogTotals = [];

        foreach ($items as $item) {
            $productId = (int) $item['parent_id'];
            $quantities[$productId] = ($quantities[$productId] ?? 0) + $item['quantity'];
            $catalogTotals[$productId] = ($catalogTotals[$productId] ?? 0) + $item['price'] * $item['quantity'];
        }

        $totalQuantity = array_sum($quantities);
        $anyProductRule = collect($rules)
            ->filter(fn (array $rule): bool => ($rule['scope'] ?? 'products') === 'any' && $rule['min_quantity'] <= $totalQuantity)
            ->sortByDesc('min_quantity')
            ->first();

        if ($anyProductRule) {
            $total = $anyProductRule['total_price'] * $totalQuantity / $anyProductRule['min_quantity'];

            return [
                'subtotal' => round($total),
                'applied_rules' => [['name' => $anyProductRule['name'], 'applications' => 1, 'quantity' => $totalQuantity, 'total' => (float) $total]],
            ];
        }

        $remaining = $quantities;
        $subtotal = 0.0;
        $appliedRules = [];
        $mixedRules = collect($rules)
            ->filter(fn (array $rule): bool => count($rule['requirements']) > 1)
            ->sortByDesc(fn (array $rule): int => array_sum(array_column($rule['requirements'], 'min_quantity')));

        foreach ($mixedRules as $rule) {
            $applications = min(array_map(
                fn (array $requirement): int => intdiv($remaining[$requirement['product_id']] ?? 0, $requirement['min_quantity']),
                $rule['requirements'],
            ));

            if ($applications < 1) {
                continue;
            }

            $quantity = 0;
            foreach ($rule['requirements'] as $requirement) {
                $consumed = $requirement['min_quantity'] * $applications;
                $remaining[$requirement['product_id']] -= $consumed;
                $quantity += $consumed;
            }

            $total = (float) ($rule['total_price'] * $applications);
            $subtotal += $total;
            $appliedRules[] = ['name' => $rule['name'], 'applications' => $applications, 'quantity' => $quantity, 'total' => $total];
        }

        ksort($remaining, SORT_NUMERIC);

        foreach ($remaining as $productId => $quantity) {
            if ($quantity < 1) {
                continue;
            }

            $productRule = collect($rules)
                ->filter(fn (array $rule): bool => count($rule['requirements']) === 1
                    && $rule['requirements'][0]['product_id'] === $productId
                    && $rule['requirements'][0]['min_quantity'] <= $quantity)
                ->sortByDesc(fn (array $rule): int => $rule['requirements'][0]['min_quantity'])
                ->first();

            if ($productRule) {
                $total = $productRule['total_price'] * $quantity / $productRule['requirements'][0]['min_quantity'];
                $appliedRules[] = ['name' => $productRule['name'], 'applications' => 1, 'quantity' => $quantity, 'total' => (float) $total];
            } else {
                $total = $catalogTotals[$productId] * $quantity / $quantities[$productId];
            }

            $subtotal += $total;
        }

        return ['subtotal' => round($subtotal), 'applied_rules' => $appliedRules];
    }
}
