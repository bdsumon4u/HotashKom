<?php

namespace App\Models;

use App\Pathao\Facade\Pathao;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
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
        'data' => '{"subtotal":0,"shipping_cost":0,"advanced":0,"discount":0,"courier":"Other","city_id":"","area_id":"","weight":0.5}',
    ];

    protected static $logFillable = true;

    public static function booted(): void
    {
        static::retrieved(function (Order $order): void {
            if (empty($order->data['city_name'] ?? '') && ! empty($order->data['city_id'] ?? '')) {
                $order->fill(['data' => ['city_name' => current(array_filter($order->getCityList(), fn ($c): bool => $c->city_id == ($order->data['city_id'] ?? '')))->city_name ?? 'N/A']]);
                $order->fill(['data' => ['area_name' => current(array_filter($order->getAreaList(), fn ($a): bool => $a->zone_id == ($order->data['area_id'] ?? '')))->zone_name ?? 'N/A']]);
                $order->save();
            }
        });

        static::saving(function (Order $order): void {
            $order->adjustStock();

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
                foreach ($order->getCityList() as $city) {
                    if ($match = $fuse->search($city->city_name)) {
                        $matches[$city->city_name] = $match[0]['score'];
                    }
                }
                if ($matches !== []) {
                    asort($matches);
                    $city = current(array_filter($order->getCityList(), fn ($c): bool => $c->city_name === key($matches)));
                    $order->fill(['data' => ['city_id' => $city->city_id, 'city_name' => $city->city_name ?? 'N/A']]);
                }
            } else {
                $order->fill(['data' => ['city_name' => current(array_filter($order->getCityList(), fn ($c): bool => $c->city_id == ($order->data['city_id'] ?? '')))->city_name ?? 'N/A']]);
            }

            if (false) {
                $matches = [];
                foreach ($order->getAreaList() as $area) {
                    if ($match = $fuse->search($area->zone_name)) {
                        $matches[$area->zone_name] = $match[0]['score'];
                    }
                }
                if ($matches !== []) {
                    asort($matches);
                    $area = current(array_filter($order->getAreaList(), fn ($a): bool => $a->zone_name === key($matches)));
                    $order->fill(['data' => ['area_id' => $area->zone_id, 'area_name' => $area->zone_name ?? 'N/A']]);
                }
            } else {
                $order->fill(['data' => ['area_name' => current(array_filter($order->getAreaList(), fn ($a): bool => $a->zone_id == $order->data['area_id']))->zone_name ?? 'N/A']]);
            }
        });
    }

    public function adjustStock(): void
    {
        if ($this->exists && ! $this->isDirty('status')) {
            return;
        }

        $increment = ['PENDING', 'WAITING', 'RETURNED', 'CANCELLED'];
        $decrement = ['CONFIRMED', 'INVOICED', 'SHIPPING', 'COMPLETED', 'LOST'];

        $prev = $this->getOriginal('status');
        $next = $this->getAttribute('status');

        // if both prev and next belongs to same group, then no need to adjust stock
        if (in_array($prev, $increment) && in_array($next, $increment)) {
            return;
        }
        if (in_array($prev, $decrement) && in_array($next, $decrement)) {
            return;
        }

        $fact = 1;
        if (in_array($next, $decrement)) {
            $fact = -1;
        }

        $DBproducts = Product::where('should_track', true)->find(array_keys($products = (array) $this->products));

        foreach ($DBproducts as $product) {
            $product->increment('stock_count', $fact * $products[$product->id]->quantity);
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
            ->logOnly(['name', 'phone', 'status', 'data->courier', 'data->advanced', 'data->discount', 'data->shipping_cost', 'data->subtotal']);
    }

    public function getCityList()
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

    public function getAreaList()
    {
        if (! (setting('Pathao')->enabled ?? false)) {
            return [];
        }

        $areaList = [];
        $exception = false;
        if ($this->data['city_id'] ?? false) {
            $areaList = cache()->remember('pathao_areas:'.$this->data['city_id'], now()->addDay(), function () use (&$exception) {
                try {
                    return Pathao::area()->zone($this->data['city_id'])->data;
                } catch (\Exception) {
                    $exception = true;

                    return [];
                }
            });
        }

        if ($exception) {
            cache()->forget('pathao_areas:'.$this->data['city_id']);
        }

        return $areaList;
    }
}
