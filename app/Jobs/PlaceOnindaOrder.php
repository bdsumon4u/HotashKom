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
        Log::info('Starting PlaceOnindaOrder job', [
            'orderId' => $this->orderId,
            'domain' => $this->domain,
        ]);

        try {
            // ===== ONINDA DATABASE OPERATIONS (DEFAULT CONNECTION) =====

            // Find reseller in Oninda database
            $reseller = User::where('domain', $this->domain)->first();
            if (! $reseller) {
                Log::error("Reseller not found in Oninda database for domain {$this->domain}");

                return;
            }

            // Check if order already exists in Oninda database
            $existingOnindaOrder = $reseller->orders()->where('source_id', $this->orderId)->first();
            if ($existingOnindaOrder) {
                Log::info("Order {$this->orderId} already placed on Oninda");

                $this->configureResellerDatabaseConnection($reseller);
                $this->updateResellerOrderSourceId($this->orderId, $existingOnindaOrder->id);

                return;
            }

            // Configure reseller database connection
            $this->configureResellerDatabaseConnection($reseller);

            // Test reseller connection
            try {
                DB::connection('reseller')->getPdo();
            } catch (\Exception $e) {
                Log::error('Failed to connect to reseller database', [
                    'domain' => $this->domain,
                    'error' => $e->getMessage(),
                ]);

                return;
            }

            // ===== RESELLER DATABASE OPERATIONS =====

            // Find order in reseller's database
            $resellerOrder = Order::on('reseller')->find($this->orderId);
            if (! $resellerOrder) {
                Log::error("Order {$this->orderId} not found in reseller database {$this->domain}");

                return;
            }

            // Validate reseller order data
            $products = $resellerOrder->products;
            info('products', ['products' => $products]);
            if (! $products || ! is_array($products) && ! is_object($products)) {
                Log::error("Order {$this->orderId} has no valid products data", [
                    'products' => $products,
                    'domain' => $this->domain,
                ]);

                return;
            }

            // ===== ONINDA DATABASE OPERATIONS (DEFAULT CONNECTION) =====

            // Get old orders to determine admin assignment (Oninda database)
            $oldOrders = DB::table('orders')
                ->select(['id', 'admin_id', 'status'])
                ->where('phone', $resellerOrder->phone)
                ->get();

            $adminIds = $oldOrders->pluck('admin_id')->unique()->toArray();

            // Assign admin (Oninda database)
            $admin = $this->assignAdmin($adminIds);

            // Get source_ids from reseller's products
            $sourceIds = collect($products)->pluck('source_id')->filter()->toArray();
            info('sourceIds', ['sourceIds' => $sourceIds]);

            // Get products from Oninda database
            $onindaProducts = Product::whereIn('id', $sourceIds)->get();

            // info('onindaProducts', ['onindaProducts' => $onindaProducts]);
            // Map products from Oninda database
            $mappedProducts = collect($products)->mapWithKeys(function ($product) use ($onindaProducts) {
                $onindaProduct = $onindaProducts->firstWhere('id', $product->source_id);
                if (! $onindaProduct) {
                    return null;
                }

                $cartItem = (new ProductResource($onindaProduct))->toCartItem($product->quantity);
                $cartItem['retail_price'] = $product->price;

                return [$product->source_id => $cartItem];
            })->filter()->toArray();

            // Prepare order data for Oninda database
            $orderData = $this->prepareOrderData($resellerOrder, $mappedProducts);
            $orderData['user_id'] = $reseller->id;

            // Create new order in Oninda database
            $onindaOrder = Order::create($orderData);

            Log::info('Order created in Oninda database', [
                'onindaOrderId' => $onindaOrder->id,
                'resellerOrderId' => $this->orderId,
            ]);

            if ($onindaOrder->id) {
                // ===== RESELLER DATABASE OPERATIONS =====

                info('updating source_id in reseller database', ['resellerOrderId' => $resellerOrder->id, 'onindaOrderId' => $onindaOrder->id]);
                // Update source_id in reseller's database
                $this->updateResellerOrderSourceId($resellerOrder->id, $onindaOrder->id);
                info('source_id updated in reseller database', ['resellerOrderId' => $resellerOrder->id, 'onindaOrderId' => $onindaOrder->id]);

                // ===== ONINDA DATABASE OPERATIONS (DEFAULT CONNECTION) =====

                // Update admin's last_order_received_at
                DB::table('admins')
                    ->where('id', $admin->id)
                    ->update(['last_order_received_at' => now()]);

                Log::info("Successfully placed order {$this->orderId} on Oninda as order {$onindaOrder->id}");
            }

        } catch (\Exception $e) {
            Log::error("Failed to place order {$this->orderId} on Oninda", [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            throw $e;
        } finally {
            // Always purge the reseller connection to free up resources
            DB::purge('reseller');
        }
    }

    /**
     * Assign admin based on configuration and existing orders
     */
    private function assignAdmin(array $adminIds): object
    {
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
                $admin = $adminQ->whereIn('id', $adminIds)->first()
                    ?? $adminQ->first()
                    ?? DB::table('admins')->where('is_active', true)->inRandomOrder()->first();
            } else {
                $admin = $adminQ->first()
                    ?? DB::table('admins')->where('is_active', true)->inRandomOrder()->first();
            }
        }

        return $admin;
    }

    /**
     * Prepare order data for Oninda database
     */
    private function prepareOrderData(object $resellerOrder, array $mappedProducts): array
    {
        // Get base attributes from reseller order
        $attributes = $resellerOrder->getAttributes();
        $attributes['source_id'] = $resellerOrder->id;
        unset($attributes['id']);

        // Override specific attributes
        $attributes['admin_id'] = $resellerOrder->admin_id;
        $attributes['products'] = $mappedProducts;

        // Prepare order data
        $orderData = $resellerOrder->data ?? [];
        if (! is_array($orderData)) {
            $orderData = [];
        }

        $orderData['subtotal'] = $resellerOrder->getSubtotal($mappedProducts);
        $orderData['retail_delivery_fee'] = $orderData['shipping_cost'] ?? 0;
        $orderData['retail_discount'] = $orderData['discount'] ?? 0;

        // Calculate Oninda shipping cost
        $orderData['shipping_cost'] = $resellerOrder->getShippingCost($mappedProducts, $orderData['subtotal'], $orderData['shipping_area']);
        $orderData['discount'] = 0;

        // Purchase cost
        $orderData['purchase_cost'] = $resellerOrder->getPurchaseCost($mappedProducts);

        $attributes['data'] = $orderData;

        return $attributes;
    }

    private function configureResellerDatabaseConnection($reseller): void
    {
        $resellerConfig = $reseller->getDatabaseConfig();
        config(['database.connections.reseller' => $resellerConfig]);
        DB::purge('reseller');
        DB::reconnect('reseller');
    }

    private function updateResellerOrderSourceId(int $resellerOrderId, int $onindaOrderId): void
    {
        DB::connection('reseller')->table('orders')
            ->where('id', $resellerOrderId)
            ->update(['source_id' => $onindaOrderId]);
        Log::info("Updated source_id in reseller database for order {$resellerOrderId} to Oninda order id {$onindaOrderId}");
    }
}
