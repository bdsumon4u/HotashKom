<?php

namespace App\Models;

use App\Jobs\SyncOrderStatusWithReseller;
use App\Jobs\SyncProductStockWithResellers;
use App\Pathao\Facade\Pathao;
use App\Redx\Facade\Redx;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Order extends Model
{
    use LogsActivity;

    const ONLINE = 0;

    const MANUAL = 1;

    protected $fillable = [
        'admin_id', 'user_id', 'type', 'name', 'phone', 'email', 'address', 'status', 'status_at', 'products', 'note', 'data',
    ];

    protected $attributes = [
        'status' => 'CONFIRMED',
        'data' => '{"subtotal":0,"shipping_cost":0,"retail_delivery_fee":0,"advanced":0,"discount":0,"retail_discount":0,"courier":"Other","city_id":"","area_id":"","weight":0.5}',
    ];

    protected static $logFillable = true;

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

            if (false) {
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

        static::updated(function (Order $order) {
            $status = Arr::get($order->getChanges(), 'status');

            // Dispatch job to sync status with resellers
            if ($status) {
                SyncOrderStatusWithReseller::dispatch($order->id);
            }

            if (! in_array($status, ['COMPLETED', 'RETURNED'])) {
                return;
            }
            $retail = collect($order->products)->sum(function ($product) {
                return $product->retail_price * $product->quantity;
            });

            if ($status == 'COMPLETED') {
                $amount = $retail + $order->data['retail_delivery_fee'] - $order->data['advanced'] - $order->data['retail_discount']
                    - ($order->data['subtotal'] + $order->data['shipping_cost'] - $order->data['discount']);
                $order->user->deposit($amount, [
                    'reason' => 'Order #'.$order->id.' is '.$status,
                    'order_id' => $order->id,
                ]);
            } elseif ($status == 'RETURNED') {
                $order->user->forceWithdraw($retail + $order->data['advanced'] - $order->data['retail_delivery_fee'], [
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

        if (! $fact = $sign()) {
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
            SyncProductStockWithResellers::dispatch($product);
        }
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "The order #{$this->id} has been {$eventName}";
    }

    public function products(): Attribute
    {
        return Attribute::get(fn ($products): mixed => json_decode((string) $products));
    }

    public function data(): Attribute
    {
        return Attribute::make(
            fn ($data): mixed => json_decode((string) $data, true),
            fn ($data) => $this->attributes['data'] = json_encode(array_merge($this->data, $data)),
        );
    }

    public function barcode(): Attribute
    {
        $pad = str_pad($this->id, 10, '0', STR_PAD_LEFT);

        return Attribute::get(fn (): string => substr($pad, 0, 3).'-'.substr($pad, 3, 3).'-'.substr($pad, 6, 4));
    }

    public function condition(): Attribute
    {
        return Attribute::get(fn (): int => intval($this->data['subtotal']) + intval($this->data['shipping_cost']) - intval($this->data['advanced'] ?? 0) - intval($this->data['discount'] ?? 0));
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

        return array_reduce($products, fn ($sum, $product) => $sum + ((array) $product)['total']) ?? 0;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->useLogName('orders')
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['status_at', 'updated_at'])
            ->logOnly(['admin_id', 'name', 'phone', 'address', 'status', 'status_at', 'products', 'note', 'data->courier', 'data->advanced', 'data->discount', 'data->shipping_cost', 'data->subtotal']);
    }

    public function pathaoCityList()
    {
        if (! (setting('Pathao')->enabled ?? false)) {
            return [];
        }

        $exception = false;
        $cityList = cache()->remember('pathao_cities', now()->addDay(), function () use (&$exception) {
            try {
                return Pathao::area()->city()->data;
            } catch (\Exception) {
                $exception = true;

                return [];
            }
        });

        if ($exception) {
            cache()->forget('pathao_cities');
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

            $areaList = cache()->remember('pathao_areas:'.$cityId, now()->addDay(), function () use (&$exception, &$cityId) {
                try {
                    return Pathao::area()->zone($cityId)->data;
                } catch (\Exception) {
                    $exception = true;

                    return [];
                }
            });
        }

        if ($exception) {
            cache()->forget('pathao_areas:'.$cityId);
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
        $areaList = cache()->remember('redx_areas', now()->addDay(), function () use (&$exception) {
            try {
                return Redx::area()->list()->areas;
            } catch (\Exception) {
                $exception = true;

                return [];
            }
        });

        if ($exception) {
            cache()->forget('redx_areas');
        }

        return $areaList;
    }
}
