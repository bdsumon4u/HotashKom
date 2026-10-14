<?php

namespace App\Actions\Tenant;

use App\Http\Resources\ProductResource;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ForwardTenantOrdersAction
{
    /**
     * Forward orders from a tenant store to the central wholesaler platform.
     *
     * @param  Collection<int, Order>|array<int, Order>  $orders
     * @return int Number of orders successfully forwarded
     */
    public function execute(Tenant $tenant, User $reseller, Collection|array $orders): int
    {
        $forwardedCount = 0;

        foreach ($orders as $tenantOrder) {
            // Ensure we have fresh model instance and valid state
            if ($tenantOrder->status !== 'CONFIRMED' || $tenantOrder->source_id) {
                continue;
            }

            // Check if already created on central platform
            $existingCentralOrder = Order::withoutTenancy()
                ->whereNull('tenant_id')
                ->where('user_id', $reseller->id)
                ->where('source_id', $tenantOrder->id)
                ->first();

            if ($existingCentralOrder) {
                $tenantOrder->update(['source_id' => $existingCentralOrder->id]);
                $forwardedCount++;

                continue;
            }

            $products = $tenantOrder->products;
            if (! $products) {
                continue;
            }

            // Map products to central catalog
            $mappedProducts = $this->mapProductsToCentral($products);
            if (empty($mappedProducts)) {
                Log::warning("Could not map any products for tenant order #{$tenantOrder->id}");

                continue;
            }

            // Assign central admin
            $assignedAdmin = $this->assignAdmin($tenantOrder->phone);

            // Prepare order data
            $orderData = $this->prepareOrderData($tenantOrder, $mappedProducts);

            // Prepare central order attributes
            $attributes = $tenantOrder->getAttributes();
            unset($attributes['id']);

            $attributes['tenant_id'] = null;
            $attributes['user_id'] = $reseller->id;
            $attributes['source_id'] = $tenantOrder->id;
            $attributes['admin_id'] = $assignedAdmin?->id;
            $attributes['products'] = $mappedProducts;
            $attributes['data'] = $orderData;
            $attributes['tracking'] = $tenantOrder->tracking;
            $attributes['courier_report'] = $tenantOrder->courier_report;

            // Create order on central platform
            $centralOrder = Order::withoutTenancy()->create($attributes);

            if ($centralOrder && $centralOrder->id) {
                // Link central order ID back to tenant order
                $tenantOrder->update(['source_id' => $centralOrder->id]);

                if ($assignedAdmin) {
                    DB::table('admins')
                        ->where('id', $assignedAdmin->id)
                        ->update(['last_order_received_at' => now()]);
                }

                Log::info("Tenant order #{$tenantOrder->id} forwarded to central as #{$centralOrder->id} for reseller #{$reseller->id}");
                $forwardedCount++;
            }
        }

        return $forwardedCount;
    }

    /**
     * Map tenant products to central catalog products.
     */
    protected function mapProductsToCentral(mixed $products): array
    {
        $productsCollection = collect($products);
        $mapped = [];

        foreach ($productsCollection as $p) {
            $pObj = (object) $p;
            $qty = (int) ($pObj->quantity ?? 1);
            $retailPrice = (float) ($pObj->price ?? ($pObj->retail_price ?? 0));

            // Find matching product on central platform (tenant_id IS NULL)
            $centralProduct = null;

            if (! empty($pObj->source_id)) {
                $centralProduct = Product::withoutTenancy()->whereNull('tenant_id')->find($pObj->source_id);
            }

            if (! $centralProduct && ! empty($pObj->sku)) {
                $centralProduct = Product::withoutTenancy()->whereNull('tenant_id')->where('sku', $pObj->sku)->first();
            }

            if (! $centralProduct && ! empty($pObj->slug)) {
                $centralProduct = Product::withoutTenancy()->whereNull('tenant_id')->where('slug', $pObj->slug)->first();
            }

            if (! $centralProduct && ! empty($pObj->id)) {
                $centralProduct = Product::withoutTenancy()->whereNull('tenant_id')->find($pObj->id);
            }

            if ($centralProduct) {
                $cartItem = (new ProductResource($centralProduct))->toCartItem($qty);
                $cartItem['retail_price'] = $retailPrice;
                $mapped[$centralProduct->id] = $cartItem;
            } else {
                // Fallback: preserve tenant product payload if not found on central
                $cartItem = (array) $pObj;
                $cartItem['retail_price'] = $retailPrice;
                $mapped[$pObj->id ?? uniqid()] = $cartItem;
            }
        }

        return $mapped;
    }

    /**
     * Assign a central platform admin (round-robin or active salesman).
     */
    protected function assignAdmin(?string $phone): ?object
    {
        $oldOrders = ! empty($phone)
            ? DB::table('orders')->whereNull('tenant_id')->where('phone', $phone)->pluck('admin_id')->filter()->unique()->toArray()
            : [];

        if (config('app.round_robin_order_receiving')) {
            $adminQ = DB::table('admins')
                ->whereNull('tenant_id')
                ->orderByRaw('CASE WHEN is_active = 1 THEN 0 ELSE 1 END, role_id desc, last_order_received_at asc');

            return count($oldOrders) > 0 ? ($adminQ->whereIn('id', $oldOrders)->first() ?? $adminQ->first()) : $adminQ->first();
        }

        $adminQ = DB::table('admins')
            ->whereNull('tenant_id')
            ->where('role_id', Admin::SALESMAN)
            ->where('is_active', true)
            ->inRandomOrder();

        if (count($oldOrders) > 0) {
            return $adminQ->whereIn('id', $oldOrders)->first()
                ?? $adminQ->first()
                ?? DB::table('admins')->whereNull('tenant_id')->where('is_active', true)->inRandomOrder()->first();
        }

        return $adminQ->first()
            ?? DB::table('admins')->whereNull('tenant_id')->where('is_active', true)->inRandomOrder()->first();
    }

    /**
     * Prepare order data for central platform.
     */
    protected function prepareOrderData(Order $tenantOrder, array $mappedProducts): array
    {
        $orderData = $tenantOrder->data ?? [];
        if (! is_array($orderData)) {
            $orderData = [];
        }

        $subtotal = $tenantOrder->getSubtotal($mappedProducts);
        $orderData['subtotal'] = $subtotal;
        $orderData['retail_delivery_fee'] = $orderData['shipping_cost'] ?? 0;
        $orderData['retail_discount'] = $orderData['discount'] ?? 0;
        $orderData['shipping_cost'] = $tenantOrder->getShippingCost($mappedProducts, $subtotal, $orderData['shipping_area'] ?? null);
        $orderData['discount'] = 0;
        $orderData['purchase_cost'] = $tenantOrder->getPurchaseCost($mappedProducts);

        return $orderData;
    }
}
