<?php

namespace App\Jobs;

use App\Http\Resources\ProductResource;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlaceOnindaOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $orderId,
        protected string $domain
    ) {}

    public function handle(): void
    {
        info('placeOnindaOrder', ['orderId' => $this->orderId, 'domain' => $this->domain]);
        try {
            // Find reseller
            $reseller = User::where('domain', $this->domain)->first();
            if (! $reseller) {
                Log::error("Reseller not found for domain {$this->domain}");

                return;
            }

            // Configure reseller database connection
            config(['database.connections.reseller' => $reseller->getDatabaseConfig()]);

            // Purge and reconnect to ensure fresh connection
            DB::purge('reseller');
            DB::reconnect('reseller');

            // Find order in reseller's database using Eloquent
            $resellerOrder = Order::on('reseller')->find($this->orderId);
            if (! $resellerOrder) {
                Log::error("Order {$this->orderId} not found in reseller database {$this->domain}");

                return;
            }

            // Get old orders to determine admin assignment
            $oldOrders = DB::table('orders')
                ->select(['id', 'admin_id', 'status'])
                ->where('phone', $resellerOrder->phone)
                ->get();

            $adminIds = $oldOrders->pluck('admin_id')->unique()->toArray();

            if (config('app.round_robin_order_receiving')) {
                $adminQ = DB::table('admins')
                    ->orderByRaw('CASE WHEN is_active = 1 THEN 0 ELSE 1 END, role_id desc, last_order_received_at asc');
                if (count($adminIds) > 0) {
                    $admin = $adminQ->whereIn('id', $adminIds)->first() ?? $adminQ->first();
                } else {
                    $admin = $adminQ->first();
                }
            } else {
                $adminQ = DB::table('admins')
                    ->where('role_id', Admin::SALESMAN)
                    ->where('is_active', true)
                    ->inRandomOrder();
                if (count($adminIds) > 0) {
                    $admin = $adminQ->whereIn('id', $adminIds)->first() ?? $adminQ->first() ?? DB::table('admins')->where('is_active', true)->inRandomOrder()->first();
                } else {
                    $admin = $adminQ->first() ?? DB::table('admins')->where('is_active', true)->inRandomOrder()->first();
                }
            }

            // Map products from Oninda database
            $products = $resellerOrder->products;

            // Get source_ids from reseller's products
            $sourceIds = collect($products)->pluck('source_id')->filter()->toArray();

            $onindaProducts = Product::whereIn('id', $sourceIds)->get();

            $mappedProducts = collect($products)->mapWithKeys(function ($product) use ($onindaProducts) {
                $onindaProduct = $onindaProducts->firstWhere('id', $product->source_id);
                if (! $onindaProduct) {
                    return null;
                }

                $cartItem = (new ProductResource($onindaProduct))->toCartItem($product->quantity);
                $cartItem['shipping_inside'] = $onindaProduct->shipping_inside;
                $cartItem['shipping_outside'] = $onindaProduct->shipping_outside;

                // Add retail price information
                $cartItem['retail_price'] = $product->price;

                return [$product->source_id => $cartItem];
            })->filter()->toArray();

            // Create new order in Oninda database using Eloquent
            $attributes = $resellerOrder->getAttributes();
            $attributes['source_id'] = $resellerOrder->id;
            unset($attributes['id']);

            // Override specific attributes
            $attributes['user_id'] = $reseller->id;
            $attributes['admin_id'] = $admin->id;
            $attributes['products'] = json_encode($mappedProducts, JSON_UNESCAPED_UNICODE);

            // Modify data attribute
            $orderData = $resellerOrder->data;
            $orderData['subtotal'] = $resellerOrder->getSubtotal($mappedProducts);
            $orderData['retail_delivery_fee'] = $orderData['shipping_cost'];
            $orderData['retail_discount'] = $orderData['discount'] ?? 0;

            // Calculate Oninda shipping cost
            $shippingCost = 0;
            foreach ($mappedProducts as $product) {
                if ($orderData['shipping_area'] === 'Inside Dhaka') {
                    $shippingCost = max($shippingCost, $product['shipping_inside']);
                } else {
                    $shippingCost = max($shippingCost, $product['shipping_outside']);
                }
            }

            $orderData['shipping_cost'] = $shippingCost;
            $orderData['discount'] = 0;

            $attributes['data'] = $orderData;

            $onindaOrder = Order::create($attributes);

            info('oninda order created', ['onindaOrder' => $onindaOrder]);

            if ($onindaOrder->id) {
                // Update source_id in reseller's database using Eloquent
                info('updating reseller order', ['resellerOrder' => $resellerOrder, 'onindaOrder' => $onindaOrder]);
                DB::connection('reseller')->table('orders')
                    ->where('id', $resellerOrder->id)
                    ->update(['source_id' => $onindaOrder->id]);
                info('reseller order updated', ['resellerOrder' => $resellerOrder]);

                // Update admin's last_order_received_at
                DB::table('admins')
                    ->where('id', $admin->id)
                    ->update(['last_order_received_at' => now()]);

                Log::info("Successfully placed order {$this->orderId} on Oninda as order {$onindaOrder->id}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to place order {$this->orderId} on Oninda: ".$e->getMessage());
            throw $e;
        }
    }
}
