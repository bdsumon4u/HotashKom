<?php

namespace App\Models;

use App\Pathao\Facade\Pathao;
use App\Redx\Facade\Redx;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Order extends Model
{
    use LogsActivity;

    const ONLINE = 0;

    const MANUAL = 1;

    protected $fillable = [
        'admin_id', 'user_id', 'type', 'name', 'phone', 'email', 'address', 'status', 'status_at', 'shipped_at', 'products', 'note', 'data', 'source_id',
    ];

    protected $attributes = [
        'status' => 'CONFIRMED',
        'data' => '{"subtotal":0,"shipping_cost":0,"retail_delivery_fee":0,"advanced":0,"discount":0,"retail_discount":0,"courier":"Other","city_id":"","area_id":"","weight":0.5,"packaging_charge":25}',
    ];

    protected static $logFillable = true;

    #[\Override]
    public static function booted(): void
    {
        static::retrieved(function (Order $order): void {
            if (empty($order->data['city_name'] ?? '') && ! empty($order->data['city_id'] ?? '')) {
                $order->fill(['data' => ['city_name' => current(array_filter($order->pathaoCityList(), fn ($c): bool => $c->city_id == ($order->data['city_id'] ?? '')))->city_name ?? 'N/A']]);
                $order->fill(['data' => ['area_name' => current(array_filter($order->pathaoAreaList(), fn ($a): bool => $a->zone_id == ($order->data['area_id'] ?? '')))->zone_name ?? 'N/A']]);
                $order->save();
            }
        });

        static::saving(function (Order $order): void {
            info('saving');
            if (! $order->exists || $order->isDirty('status')) {
                info('does not exist or status changed');
                $order->adjustStock();
            }

            if (! $order->isDirty('data')) {
                return;
            }

            $fuse = new \Fuse\Fuse([['area' => $order->address]], [
                'keys' => ['area'],
                'includeScore' => true,
                'includeMatches' => true,
            ]);
            // Problems:
            // 1. Dhaka, Tangail, Mirzapur.
            // 2. Mirjapur, Tangal, Dhaka.
            // 3. Somethingb. Bariasomething
            // 4. Brahmanbaria => Barishal

            if (false && empty($order->data['city_id'] ?? '')) {
                $matches = [];
                foreach ($order->pathaoCityList() as $city) {
                    if ($match = $fuse->search($city->city_name)) {
                        $matches[$city->city_name] = $match[0]['score'];
                    }
                }
                if ($matches !== []) {
                    asort($matches);
                    $city = current(array_filter($order->pathaoCityList(), fn ($c): bool => $c->city_name === key($matches)));
                    $order->fill(['data' => ['city_id' => $city->city_id, 'city_name' => $city->city_name ?? 'N/A']]);
                }
            } elseif ($order->data['courier'] == 'Pathao') {
                $order->fill(['data' => ['city_name' => current(array_filter($order->pathaoCityList(), fn ($c): bool => $c->city_id == ($order->data['city_id'] ?? '')))->city_name ?? 'N/A']]);
            }

            if (false && empty($order->data['area_id'] ?? '')) {
                $matches = [];
                foreach ($order->pathaoAreaList() as $area) {
                    if ($match = $fuse->search($area->zone_name)) {
                        $matches[$area->zone_name] = $match[0]['score'];
                    }
                }
                if ($matches !== []) {
                    asort($matches);
                    $area = current(array_filter($order->pathaoAreaList(), fn ($a): bool => $a->zone_name === key($matches)));
                    $order->fill(['data' => ['area_id' => $area->zone_id, 'area_name' => $area->zone_name ?? 'N/A']]);
                }
            } elseif ($order->data['courier'] == 'Pathao') {
                $order->fill(['data' => ['area_name' => current(array_filter($order->pathaoAreaList(), fn ($a): bool => $a->zone_id == $order->data['area_id']))->zone_name ?? 'N/A']]);
            } elseif ($order->data['courier'] == 'Redx') {
                $order->fill(['data' => ['area_name' => current(array_filter($order->redxAreaList(), fn ($a): bool => $a->id == $order->data['area_id']))->name ?? 'N/A']]);
            }
        });

        static::created(function (Order $order): void {
            // Clear EditOrder component caches (activities cache will be empty initially, but clear to be safe)
            cacheMemo()->forget('order_activities:'.$order->id);
        });

        static::updated(function (Order $order): void {
            // Clear EditOrder component caches
            cacheMemo()->forget('order_activities:'.$order->id);

            if (! isOninda()) {
                return;
            }

            $status = Arr::get($order->getChanges(), 'status');

            // Dispatch job to sync status with resellers
            if ($status) {
                dispatch(new \App\Jobs\SyncOrderStatusWithReseller($order->id));
            }

            if (! in_array($status, ['DELIVERED', 'RETURNED'])) {
                return;
            }

            $retail = collect($order->products)->sum(fn ($product): float => (float) $product->retail_price * (int) $product->quantity);
            $shippingCost = (float) ($order->data['shipping_cost'] ?? 0); // Oninda's delivery fee
            $retailDeliveryFee = (float) ($order->data['retail_delivery_fee'] ?? 0); // Reseller's delivery fee charged to customer
            $advanced = (float) ($order->data['advanced'] ?? 0);
            $retailDiscount = (float) ($order->data['retail_discount'] ?? 0);
            $subtotal = (float) ($order->data['subtotal'] ?? 0);
            $discount = (float) ($order->data['discount'] ?? 0);
            $packagingCharge = (float) ($order->data['packaging_charge'] ?? 25); // Packaging charge

            $calculateCommission = (fn (): float =>
                // Commission = (retail + retail_delivery_fee) - advanced - retail_discount - (subtotal + shipping_cost - discount) - packaging_charge
                $retail
                + $retailDeliveryFee
                - $advanced
                - $retailDiscount
                - ($subtotal + $shippingCost - $discount)
                - $packagingCharge);

            if ($status === 'DELIVERED') {
                $amount = $calculateCommission();
                if ($amount < 0) {
                    $order->user->forceWithdraw(-$amount, [
                        'reason' => 'Order #'.$order->id.' is '.$status,
                        'order_id' => $order->id,
                    ]);

                    return;
                } else {
                    $order->user->deposit($amount, [
                        'reason' => 'Order #'.$order->id.' is '.$status,
                        'order_id' => $order->id,
                    ]);
                }
            } elseif ($status === 'PAID_RETURN') {
                $withdrawAmount = $packagingCharge;
                $order->user->forceWithdraw($withdrawAmount, [
                    'reason' => 'Order #'.$order->id.' is '.$status,
                    'order_id' => $order->id,
                ]);
            } elseif ($status === 'RETURNED' && $order->getOriginal('status') !== 'PAID_RETURN') {
                $withdrawAmount = $shippingCost + $packagingCharge; // Charge shipping cost and packaging fee
                if ($order->getOriginal('status') === 'DELIVERED') {
                    // Withdraw commission (what was paid on delivery) and Oninda's delivery fee and packaging charge
                    $withdrawAmount += $calculateCommission();
                }

                $order->user->forceWithdraw($withdrawAmount, [
                    'reason' => 'Order #'.$order->id.' is '.$status,
                    'order_id' => $order->id,
                ]);
            }
        });
    }

    public function adjustStock(): void
    {
        info('adjusting stock', ['order' => $this->id]);
        $sign = function () {
            $increment = config('app.increment');
            $decrement = config('app.decrement');
            if (! $this->exists) {
                info('does not exist');
                if (in_array($this->status, $decrement)) {
                    return -1;
                }

                return 0;
            }

            info('exists');
            $prev = $this->getOriginal('status');
            $next = $this->getAttribute('status');

            // if both prev and next belongs to same group, then no need to adjust stock
            if (in_array($prev, $increment) && in_array($next, $increment)) {
                return 0;
            }
            if (in_array($prev, $decrement) && in_array($next, $decrement)) {
                return 0;
            }

            if (in_array($next, $decrement)) {
                return -1;
            }

            return 1;
        };

        if ((($fact = $sign())) === 0) {
            info('no fact');

            return;
        }

        info('fact', ['fact' => $fact]);
        info('products', ['products' => $this->products]);
        info('x', array_keys($products = (array) $this->products));
        info('x', $products);
        $DBproducts = Product::where('should_track', true)->find(array_keys($products = (array) $this->products));

        foreach ($DBproducts as $product) {
            info('adjusting stock', ['product' => $product->id, 'fact' => $fact, 'quantity' => $products[$product->id]->quantity]);
            $increment = $fact * $products[$product->id]->quantity;
            $product->increment('stock_count', $increment);
            info('incremented', ['product' => $product->id, 'increment' => $increment]);
            // Dispatch job to sync stock with resellers
            dispatch(new \App\Jobs\SyncProductStockWithResellers($product));
        }
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "The order #{$this->id} has been {$eventName}";
    }

    protected function products(): Attribute
    {
        return Attribute::get(fn ($products): mixed => json_decode((string) $products));
    }

    protected function data(): Attribute
    {
        return Attribute::make(
            fn ($data): mixed => json_decode((string) $data, true),
            fn ($data) => $this->attributes['data'] = json_encode(array_merge($this->data, $data)),
        );
    }

    protected function barcode(): Attribute
    {
        $pad = str_pad($this->id, 10, '0', STR_PAD_LEFT);

        return Attribute::get(fn (): string => substr($pad, 0, 3).'-'.substr($pad, 3, 3).'-'.substr($pad, 6, 4));
    }

    protected function condition(): Attribute
    {
        return Attribute::get(function (): int {
            $retail = 0;
            foreach ((array) $this->products as $product) {
                $quantity = (int) ($product->quantity ?? 0);
                $price = (isOninda() && config('app.resell'))
                    ? ($product->retail_price ?? $product->price ?? 0)
                    : ($product->price ?? 0);
                $retail += $quantity * (float) $price;
            }

            $deliveryFee = (isOninda() && config('app.resell'))
                ? ($this->data['retail_delivery_fee'] ?? $this->data['shipping_cost'] ?? 0)
                : ($this->data['shipping_cost'] ?? 0);
            $deliveryFee = (float) $deliveryFee;

            $discount = (isOninda() && config('app.resell'))
                ? ($this->data['retail_discount'] ?? 0)
                : ($this->data['discount'] ?? 0);
            $discount = (float) $discount;

            $couponDiscount = (float) ($this->data['coupon_discount'] ?? 0);
            if (isOninda() && config('app.resell')) {
                // Coupon discount only applies on buying price, not reseller selling price.
                $couponDiscount = 0.0;
            }

            $advanced = (float) ($this->data['advanced'] ?? 0);

            return (int) round($retail + $deliveryFee - $discount - $advanced - $couponDiscount);
        });
    }

    /**
     * Get retail amounts for the order with fallbacks for old orders
     */
    public function getRetailAmounts(): array
    {
        $retailSubtotal = 0;
        foreach ((array) $this->products as $product) {
            $quantity = (int) ($product->quantity ?? 0);
            // Fallback: if retail_price is not available, use wholesale price
            $retailPrice = (float) ($product->retail_price ?? $product->price ?? 0);
            $retailSubtotal += $quantity * $retailPrice;
        }

        // Fallback: if retail_delivery_fee is not available, use shipping_cost
        $retailDeliveryFee = (float) ($this->data['retail_delivery_fee'] ?? $this->data['shipping_cost'] ?? 0);
        // Fallback: if retail_discount is not available, use discount or 0
        $retailDiscount = (float) ($this->data['retail_discount'] ?? $this->data['discount'] ?? 0);
        $retailTotal = $retailSubtotal + $retailDeliveryFee - $retailDiscount;

        return [
            'retail_subtotal' => $retailSubtotal,
            'retail_delivery_fee' => $retailDeliveryFee,
            'retail_total' => $retailTotal,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class)->withDefault([
            'name' => 'System',
        ]);
    }

    public function getSubtotal($products)
    {
        $products = (array) $products;

        return array_reduce($products, function ($sum, $product) {
            $product = (array) $product;

            return $sum + ($product['total'] ?? 0);
        }, 0);
    }

    public function getPurchaseCost($products)
    {
        $products = (array) $products;

        return array_reduce($products, function ($sum, $product) {
            $product = (array) $product;
            $purchasePrice = $product['purchase_price'] ?? $product['price'] ?? 0;
            $quantity = $product['quantity'] ?? 0;

            return $sum + ($purchasePrice * $quantity);
        }, 0);
    }

    public function getShippingCost($products, $subtotal = 0, ?string $shipping_area = null)
    {
        if (! $products instanceof Collection) {
            $products = collect($products);
        }

        $this->isFreeDelivery = false;
        $shipping_cost = 0;
        if ($shipping_area) {
            if (setting('show_option')->productwise_delivery_charge ?? false) {
                $shipping_cost = $products->sum(function ($item) use ($shipping_area) {
                    $item = (array) $item;
                    $factor = (setting('show_option')->quantitywise_delivery_charge ?? false) ? ($item['quantity'] ?? 1) : 1;

                    return ($item[$shipping_area === 'Inside Dhaka' ? 'shipping_inside' : 'shipping_outside'] ?? 0) * $factor;
                }) ?: setting('delivery_charge')->{$shipping_area === 'Inside Dhaka' ? 'inside_dhaka' : 'outside_dhaka'} ?? 0;
            } else {
                $shipping_cost = $products->max(function ($item) use ($shipping_area) {
                    $item = (array) $item;
                    $factor = (setting('show_option')->quantitywise_delivery_charge ?? false) ? ($item['quantity'] ?? 1) : 1;

                    return ($item[$shipping_area === 'Inside Dhaka' ? 'shipping_inside' : 'shipping_outside'] ?? 0) * $factor;
                }) ?: setting('delivery_charge')->{$shipping_area === 'Inside Dhaka' ? 'inside_dhaka' : 'outside_dhaka'} ?? 0;
            }
        }

        $freeDelivery = setting('free_delivery');

        if (! ($freeDelivery->enabled ?? false) || ($freeDelivery->enabled ?? false) == 'false') {
            return $shipping_cost;
        }

        if ($freeDelivery->for_all ?? false) {
            if ($subtotal < $freeDelivery->min_amount) {
                return $shipping_cost;
            }
            $quantity = $products->sum(function ($product) {
                $product = (array) $product;

                return $product['quantity'] ?? 0;
            });
            if ($quantity < $freeDelivery->min_quantity) {
                return $shipping_cost;
            }

            $this->isFreeDelivery = true;

            return 0;
        }

        foreach ((array) ($freeDelivery->products ?? []) as $id => $qty) {
            if ($products->where('parent_id', $id)->where('quantity', '>=', $qty)->count()) {
                $this->isFreeDelivery = true;

                return 0;
            }
        }

        return $shipping_cost;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->useLogName('orders')
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['status_at', 'updated_at'])
            ->logOnly(['admin_id', 'name', 'phone', 'address', 'status', 'status_at', 'products', 'note', 'data->courier', 'data->advanced', 'data->discount', 'data->shipping_cost', 'data->subtotal', 'data->packaging_charge']);
    }

    public function pathaoCityList()
    {
        if (! (setting('Pathao')->enabled ?? false)) {
            return [];
        }

        // Try to get from cache first
        $cached = cacheMemo()->get('pathao_cities');
        if ($cached !== null) {
            return $cached;
        }

        $exception = false;
        $cityList = cacheMemo()->remember('pathao_cities', now()->addDay(), function () use (&$exception) {
            try {
                return Pathao::area()->city()->data;
            } catch (\Exception $e) {
                $exception = true;
                // Log the error for debugging
                logger()->warning('Pathao API error when fetching cities', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return [];
            }
        });

        if ($exception) {
            cacheMemo()->forget('pathao_cities');
        }

        return $cityList;
    }

    public function pathaoAreaList($cityId = null)
    {
        if (! (setting('Pathao')->enabled ?? false)) {
            return [];
        }

        $areaList = [];
        $exception = false;
        $cityId ??= $this->data['city_id'] ?? false;
        if ($cityId) {
            // Try to get from cache first
            $cached = cacheMemo()->get('pathao_areas:'.$cityId);
            if ($cached !== null) {
                return $cached;
            }

            $areaList = cacheMemo()->remember('pathao_areas:'.$cityId, now()->addDay(), function () use (&$exception, &$cityId) {
                try {
                    return Pathao::area()->zone($cityId)->data;
                } catch (\Exception $e) {
                    $exception = true;
                    // Log the error for debugging
                    logger()->warning('Pathao API error when fetching areas', [
                        'city_id' => $cityId,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);

                    return [];
                }
            });
        }

        if ($exception) {
            cacheMemo()->forget('pathao_areas:'.$cityId);
        }

        return $areaList;
    }

    public function redxAreaList()
    {
        if (! (setting('Redx')->enabled ?? config('redx.enabled'))) {
            return [];
        }

        $areaList = [];
        $exception = false;
        $areaList = cacheMemo()->remember('redx_areas', now()->addDay(), function () use (&$exception) {
            try {
                return Redx::area()->list()->areas;
            } catch (\Exception) {
                $exception = true;

                return [];
            }
        });

        if ($exception) {
            cacheMemo()->forget('redx_areas');
        }

        return $areaList;
    }

    protected function casts(): array
    {
        return [
            'source_id' => 'integer',
            'user_id' => 'integer',
            'admin_id' => 'integer',
            'products' => 'array',
            'data' => 'array',
            'status_at' => 'datetime',
            'shipped_at' => 'datetime',
        ];
    }
}
