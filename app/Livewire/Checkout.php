<?php

namespace App\Livewire;

use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\User\AccountCreated;
use App\Notifications\User\OrderPlaced;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\GoogleTagManager\GoogleTagManagerFacade;

class Checkout extends Component
{
    public ?Order $order = null;

    public $isFreeDelivery = false;

    public $name = '';

    public $phone = '';

    public $shipping = '';

    public $address = '';

    public $note = '';

    protected $listeners = ['updateField'];

    public $retail = [];

    public $retailDeliveryFee = 0;

    #[Validate('required|numeric|min:0')]
    public $advanced = 0;

    public function updateField($field, $value): void
    {
        $this->$field = $value;

        longCookie($field, $value);

        // I don't know how, but it works.
        // $this->updatedShipping(); // doesn't work.
    }

    public function remove($id): void
    {
        cart()->remove($id);
        $this->cartUpdated();
    }

    public function increaseQuantity($id): void
    {
        $item = cart()->get($id);
        if ($item->qty < $item->options->max || $item->options->max === -1) {
            cart()->update($id, $item->qty + 1);
            $this->cartUpdated();
        }
    }

    public function decreaseQuantity($id): void
    {
        $item = cart()->get($id);
        if ($item->qty > 1) {
            cart()->update($id, $item->qty - 1);
            $this->cartUpdated();
        }
    }

    public function shippingCost(?string $area = null)
    {
        $this->isFreeDelivery = false;
        $area ??= $this->shipping;
        $shipping_cost = 0;
        if ($area) {
            /*
            if (! (setting('show_option')->productwise_delivery_charge ?? false)) {
            */
            $shipping_cost = cart()->content()->max(function ($item) use ($area) {
                if ($area == 'Inside Dhaka') {
                    return $item->options->shipping_inside;
                } else {
                    return $item->options->shipping_outside;
                }
            });
            /*
            } else {
                $shipping_cost = cart()->content()->sum(function ($item) use ($area) {
                    if ($area == 'Inside Dhaka') {
                        return $item->options->shipping_inside * ((setting('show_option')->quantitywise_delivery_charge ?? false) ? $item->qty : 1);
                    } else {
                        return $item->options->shipping_outside * ((setting('show_option')->quantitywise_delivery_charge ?? false) ? $item->qty : 1);
                    }
                });
            }
            */
        }

        $freeDelivery = setting('free_delivery');

        if (! ($freeDelivery->enabled ?? false)) {
            return $shipping_cost;
        }

        if ($freeDelivery->for_all ?? false) {
            if (cart()->subTotal() < $freeDelivery->min_amount) {
                return $shipping_cost;
            }
            $quantity = cart()->content()->sum(fn ($product) => $product->qty);
            if ($quantity < $freeDelivery->min_quantity) {
                return $shipping_cost;
            }

            $this->isFreeDelivery = true;

            return 0;
        }

        foreach ((array) ($freeDelivery->products ?? []) as $id => $qty) {
            if (cart()->content()->where('options.parent_id', $id)->where('qty', '>=', $qty)->count()) {
                $this->isFreeDelivery = true;

                return 0;
            }
        }

        return $shipping_cost;
    }

    public function updatedShipping(): void
    {
        $this->retailDeliveryFee = $this->shippingCost();
        cart()->addCost('deliveryFee', $this->retailDeliveryFee);
    }

    public function cartUpdated(): void
    {
        $this->updatedShipping();
        $this->retail = cart()->content()->mapWithKeys(fn ($item) => [$item->id => [
            'price' => $item->options->retail_price,
            'quantity' => $item->qty,
        ]])->toArray();
        $this->dispatch('cartUpdated');
    }

    public function mount(): void
    {
        // if (!(setting('show_option')->hide_phone_prefix ?? false)) {
        //     $this->phone = '+880';
        // }

        $default_area = setting('default_area');
        if ($default_area->inside ?? false) {
            $shipping = 'Inside Dhaka';
        }
        if ($default_area->outside ?? false) {
            $shipping = 'Outside Dhaka';
        }

        if ($user = auth('user')->user()) {
            $this->name = $user->name;
            if ($user->phone_number) {
                $this->phone = Str::after($user->phone_number, '+880');
            }
            $this->address = $user->address ?? '';
            $this->note = $user->note ?? '';
        } else {
            $this->name = Cookie::get('name', '');
            $this->shipping = Cookie::get('shipping', $shipping ?? '');
            $this->phone = Cookie::get('phone', '');
            $this->address = Cookie::get('address', '');
            $this->note = Cookie::get('note', '');
        }

        $this->cartUpdated();
    }

    public function checkout()
    {
        if (! auth('user')->check()) {
            $this->dispatch('notify', ['message' => 'Please login to add product to cart', 'type' => 'error']);

            return redirect()->route('user.login')->with('danger', 'Please login to add product to cart');
        }

        if (! ($hidePrefix = setting('show_option')->hide_phone_prefix ?? false)) {
            if (Str::startsWith($this->phone, '01')) {
                $this->phone = Str::after($this->phone, '0');
            }
        } elseif (Str::startsWith($this->phone, '01')) { // hide prefix
            $this->phone = '+88'.$this->phone;
        }

        $data = $this->validate([
            'name' => 'required',
            'phone' => $hidePrefix ? 'required|regex:/^\+8801\d{9}$/' : 'required|regex:/^1\d{9}$/',
            'address' => 'required',
            'note' => 'nullable',
            'shipping' => 'required',
        ]);

        if (! $hidePrefix) {
            $data['phone'] = '+880'.$data['phone'];
        }

        throw_if(cart()->count() === 0, ValidationException::withMessages(['products' => 'Your cart is empty.']));

        $fraud = setting('fraud');

        if (
            Cache::get('fraud:hourly:'.request()->ip()) >= ($fraud->allow_per_hour ?? 3)
            || Cache::get('fraud:hourly:'.$data['phone']) >= ($fraud->allow_per_hour ?? 3)
            || Cache::get('fraud:daily:'.request()->ip()) >= ($fraud->allow_per_day ?? 7)
            || Cache::get('fraud:daily:'.$data['phone']) >= ($fraud->allow_per_day ?? 7)
        ) {
            return redirect()->back()->with('error', 'প্রিয় গ্রাহক, আরও অর্ডার করতে চাইলে আমাদের হেল্প লাইন '.setting('company')->phone.' নাম্বারে কল দিয়ে সরাসরি কথা বলুন।');
        }

        $this->order = DB::transaction(function () use ($data, &$order, $fraud) {
            $products = Product::find(cart()->content()->pluck('id'))
                ->mapWithKeys(function (Product $product) use ($fraud) {
                    $id = $product->id;
                    $quantity = min(cart($id)->qty, $fraud->max_qty_per_product ?? 3);

                    if ($quantity <= 0) {
                        return null;
                    }

                    // Needed Attributes
                    return [$id => [
                        'id' => $id,
                        'name' => $product->var_name,
                        'slug' => $product->slug,
                        'image' => optional($product->base_image)->src,
                        'price' => $selling = $product->getPrice($quantity),
                        'retail_price' => $this->retail[$id]['price'] ?? $selling,
                        'quantity' => $quantity,
                        'category' => $product->category,
                        'total' => $quantity * $selling,
                    ]];
                })->filter(function ($product) {
                    return $product != null; // Only Available Products
                })->toArray();

            if (empty($products)) {
                return $this->dispatch('notify', ['message' => 'All products are out of stock.', 'type' => 'danger']);
            }

            $data['products'] = json_encode($products, JSON_UNESCAPED_UNICODE);
            $user = $this->getUser($data);
            $oldOrders = $user->orders()->get();
            $status = data_get(config('app.orders', []), 0, 'PENDING'); // Default Status

            $oldOrders = Order::select(['id', 'admin_id', 'status'])->where('phone', $data['phone'])->get();
            $adminIds = $oldOrders->pluck('admin_id')->unique()->toArray();
            $adminQ = Admin::where('role_id', Admin::SALESMAN)->where('is_active', true)->inRandomOrder();
            if (count($adminIds) > 0) {
                $data['admin_id'] = $adminQ->whereIn('id', $adminIds)->first()->id ?? $adminQ->first()->id ?? Admin::where('is_active', true)->inRandomOrder()->first()->id;
            } else {
                $data['admin_id'] = $adminQ->first()->id ?? Admin::where('is_active', true)->inRandomOrder()->first()->id;
            }

            $data += [
                'user_id' => $user->id, // If User Logged In
                'status' => $status,
                'status_at' => now()->toDateTimeString(),
                // Additional Data
                'data' => [
                    'courier' => 'Other',
                    'is_fraud' => $oldOrders->whereIn('status', ['CANCELLED', 'RETURNED'])->count() > 0,
                    'is_repeat' => $oldOrders->count() > 0,
                    'shipping_area' => $data['shipping'],
                    'shipping_cost' => $this->shippingCost(),
                    'retail_delivery_fee' => $this->retailDeliveryFee,
                    'advanced' => $this->advanced,
                    'subtotal' => cart()->subtotal(),
                ],
            ];

            $order = Order::create($data);
            $user->notify(new OrderPlaced($order));

            GoogleTagManagerFacade::flash([
                'event' => 'purchase',
                'ecommerce' => [
                    'currency' => 'BDT',
                    'transaction_id' => $order->id,
                    'value' => $order->data['subtotal'],
                    'items' => array_values(array_map(fn ($product): array => [
                        'item_id' => $product['id'],
                        'item_name' => $product['name'],
                        'item_category' => $product['category'],
                        'price' => $product['price'],
                        'quantity' => $product['quantity'],
                    ], $products)),
                ],
            ]);

            return $order;
        });

        if (! $this->order) {
            return back();
        }

        deleteOrUpdateCart();

        Cache::add('fraud:hourly:'.request()->ip(), 0, now()->addHour());
        Cache::add('fraud:daily:'.request()->ip(), 0, now()->addDay());

        Cache::increment('fraud:hourly:'.request()->ip());
        Cache::increment('fraud:daily:'.request()->ip());

        Cache::add('fraud:hourly:'.$data['phone'], 0, now()->addHour());
        Cache::add('fraud:daily:'.$data['phone'], 0, now()->addDay());

        Cache::increment('fraud:hourly:'.$data['phone']);
        Cache::increment('fraud:daily:'.$data['phone']);

        // Undefined index email.
        // $data['email'] && Mail::to($data['email'])->queue(new OrderPlaced($order));

        cart()->destroy();
        session()->flash('completed', 'Dear '.$data['name'].', Your Order is Successfully Recieved. Thanks For Your Order.');

        return redirect()->route('thank-you', [
            'order' => optional($this->order)->getKey(),
        ]);
    }

    private function getUser($data)
    {
        if ($user = auth('user')->user()) {
            return $user;
        }

        // $user->notify(new AccountCreated());

        return User::query()->firstOrCreate(
            ['phone_number' => $data['phone']],
            array_merge(Arr::except($data, 'phone'), [
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ])
        );
    }

    public function render()
    {
        return view('livewire.checkout', [
            'user' => optional(auth('user')->user()),
        ]);
    }
}
