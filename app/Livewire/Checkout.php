<?php

namespace App\Livewire;

use App\Http\Resources\ProductResource;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\User\AccountCreated;
use App\Notifications\User\OrderPlaced;
use App\Services\FacebookPixelService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;

use function Illuminate\Support\defer;

class Checkout extends Component
{
    public ?Order $order = null;

    public $isFreeDelivery = false;

    public $name = '';

    public $phone = '';

    public $shipping = '';

    public $address = '';

    public $note = '';

    public $city_id = '';

    public $area_id = '';

    protected $listeners = ['updateField'];

    public array $retail = [];

    public $retailDeliveryFee = 0;

    #[Validate('required|numeric|min:0')]
    public $advanced = 0;

    #[Validate('nullable|numeric|min:0')]
    public $retailDiscount = 0;

    protected $facebookService;

    public function boot(FacebookPixelService $facebookService): void
    {
        $this->facebookService = $facebookService;
    }

    public function hydrate(): void
    {
        if (app()->environment('local') && request()->has('calls')) {
            logger()->debug('Checkout Livewire call queue', [
                'calls' => request()->input('calls'),
                'updates' => request()->input('updates'),
            ]);
        }
    }

    public function updateField($field, $value): void
    {
        if ($field === 'retail' && is_array($value)) {
            // Merge the retail array to preserve existing values
            $this->retail = array_merge($this->retail, $value);
        } else {
            $this->$field = $value;
            longCookie($field, $value);
        }

        // I don't know how, but it works.
        // $this->updatedShipping(); // doesn't work.
    }

    public function updatedCityId($value): void
    {
        longCookie('city_id', $value);

        // Reset area_id when city changes
        $this->area_id = '';
        longCookie('area_id', '');
    }

    public function updatedAreaId($value): void
    {
        longCookie('area_id', $value);
    }

    public function remove($id): void
    {
        cart()->remove($id);
        // $this->cartUpdated();
    }

    public function increaseQuantity($id): void
    {
        $item = cart()->get($id);
        if ($item->qty < $item->options->max || $item->options->max === -1) {
            $qty = $item->qty + 1;
            $content = cart()->content();
            $product = Product::find($item->id);
            $item->price = $price = $product->getPrice($qty);
            $item->options->retail_price = $price;
            $content->put($item->rowId, $item);
            // session()->put(cart()->currentInstance(), $content);

            cart()->update($id, $item->qty + 1);
            // $this->cartUpdated();
        }
    }

    public function decreaseQuantity($id): void
    {
        $item = cart()->get($id);
        if ($item->qty > 1) {
            $qty = $item->qty - 1;
            $content = cart()->content();
            $product = Product::find($item->id);
            $item->price = $price = $product->getPrice($qty);
            $item->options->retail_price = $price;
            $content->put($item->rowId, $item);
            // session()->put(cart()->currentInstance(), $content);

            cart()->update($id, $qty);
            // $this->cartUpdated();
        }
    }

    public function shippingCost(?string $area = null)
    {
        if (! cart()->subTotal()) {
            return 0;
        }

        $this->isFreeDelivery = false;
        $area ??= $this->shipping;
        $shipping_cost = 0;
        if ($area) {
            if (setting('show_option')->productwise_delivery_charge ?? false) {
                $shipping_cost = cart()->content()->sum(function ($item) use ($area) {
                    $factor = (setting('show_option')->quantitywise_delivery_charge ?? false) ? $item->qty : 1;
                    if ($area == 'Inside Dhaka') {
                        return $item->options->shipping_inside * $factor;
                    } else {
                        return $item->options->shipping_outside * $factor;
                    }
                });
            } else {
                $shipping_cost = cart()->content()->max(function ($item) use ($area) {
                    $factor = (setting('show_option')->quantitywise_delivery_charge ?? false) ? $item->qty : 1;
                    if ($area == 'Inside Dhaka') {
                        return $item->options->shipping_inside * $factor;
                    } else {
                        return $item->options->shipping_outside * $factor;
                    }
                });
            }
        }

        $freeDelivery = setting('free_delivery');

        if (! ((bool) ($freeDelivery->enabled ?? false)) || ($freeDelivery->enabled ?? false) == 'false') {
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
        if (! cart()->getCost('deliveryFee')) {
            cart()->addCost('deliveryFee', $this->shippingCost($this->shipping));
        }

        if (isOninda() && config('app.resell') && auth('user')->check()) {
            /** @var User $reseller */
            $reseller = auth('user')->user();
            $this->retailDeliveryFee = $reseller->getShippingCost($this->shipping) ?: cart()->getCost('deliveryFee');
        }
    }

    public function cartUpdated(): void
    {
        $this->updatedShipping();
        $this->retail = cart()->content()->mapWithKeys(fn ($item): array => [
            (string) $item->id => [
                'price' => $this->retail[$item->id]['price'] ?? $item->options->retail_price ?? 0,
                'quantity' => $item->qty,
            ],
        ])->all();
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
            $this->retailDeliveryFee = $this->shippingCost($shipping);
        }
        if ($default_area->outside ?? false) {
            $shipping = 'Outside Dhaka';
            $this->retailDeliveryFee = $this->shippingCost($shipping);
        }

        if ((! isOninda() || ! config('app.resell')) && $user = auth('user')->user()) {
            $this->name = $user->name;
            if ($user->phone_number) {
                $this->phone = Str::after($user->phone_number, '+880');
            }
            $this->address = $user->address ?? '';
            $this->note = $user->note ?? '';
            $this->retailDiscount = $user->discount ?? 0;
        } elseif ($this->fillFromCookie()) {
            $this->name = Cookie::get('name', '');
            $this->shipping = Cookie::get('shipping', $shipping ?? '');
            $this->phone = Cookie::get('phone', '');
            $this->address = Cookie::get('address', '');
            $this->note = Cookie::get('note', '');
            $this->retailDiscount = Cookie::get('retail_discount', 0);
            $this->city_id = Cookie::get('city_id', '');
            $this->area_id = Cookie::get('area_id', '');
        }

        // Initialize retail array properly
        $this->cartUpdated();
    }

    public function checkout()
    {
        if (isOninda() && auth('user')->guest()) {
            $this->dispatch('notify', ['message' => 'Please login to add product to cart', 'type' => 'error']);

            return to_route('user.login')->with('danger', 'Please login to add product to cart');
        }

        if (! ($hidePrefix = setting('show_option')->hide_phone_prefix ?? false)) {
            if (Str::startsWith($this->phone, '01')) {
                $this->phone = Str::after($this->phone, '0');
            }
        } elseif (Str::startsWith($this->phone, '01')) { // hide prefix
            $this->phone = '+88'.$this->phone;
        }

        $validationRules = [
            'name' => 'required',
            'phone' => $hidePrefix ? 'required|regex:/^\+8801\d{9}$/' : 'required|regex:/^1\d{9}$/',
            'address' => 'required',
            'note' => 'nullable',
            'shipping' => 'required',
            'retailDiscount' => 'nullable|numeric|min:0',
        ];

        // Add validation for city and area if Pathao is enabled and user_selects_city_area is checked
        if ((setting('Pathao')->enabled ?? false) && (setting('Pathao')->user_selects_city_area ?? false)) {
            $validationRules['city_id'] = 'required';
            $validationRules['area_id'] = 'required';
        }

        $data = $this->validate($validationRules);

        if (! $hidePrefix) {
            $data['phone'] = '+880'.$data['phone'];
        }

        throw_if(cart()->count() === 0, ValidationException::withMessages(['products' => 'Your cart is empty.']));

        $fraud = setting('fraud');

        if (
            cacheMemo()->get('fraud:hourly:'.request()->ip()) >= ($fraud->allow_per_hour ?? 3)
            || cacheMemo()->get('fraud:hourly:'.$data['phone']) >= ($fraud->allow_per_hour ?? 3)
            || cacheMemo()->get('fraud:daily:'.request()->ip()) >= ($fraud->allow_per_day ?? 7)
            || cacheMemo()->get('fraud:daily:'.$data['phone']) >= ($fraud->allow_per_day ?? 7)
        ) {
            return back()->with('error', 'প্রিয় গ্রাহক, আরও অর্ডার করতে চাইলে আমাদের হেল্প লাইন '.setting('company')->phone.' নাম্বারে কল দিয়ে সরাসরি কথা বলুন।');
        }

        $this->order = DB::transaction(function () use ($data, &$order, $fraud) {
            $data['products'] = Product::find(cart()->content()->pluck('id'))
                ->mapWithKeys(function (Product $product) use ($fraud) {
                    $id = $product->id;
                    $quantity = min(cart($id)->qty, $fraud->max_qty_per_product ?? 3);

                    if ($quantity <= 0) {
                        return null;
                    }

                    $productData = (new ProductResource($product))->toCartItem($quantity);
                    $productData['retail_price'] = $this->retail[$id]['price'] ?? $productData['price'];

                    return [$id => $productData];
                })->filter()->toArray();

            if (empty($data['products'])) {
                return $this->dispatch('notify', ['message' => 'All products are out of stock.', 'type' => 'danger']);
            }

            $user = $this->getUser($data);
            $oldOrders = $user->orders()->get();
            $status = $this->getDefaultStatus();

            $oldOrders = Order::select(['id', 'admin_id', 'status'])->where('phone', $data['phone'])->get();
            $adminIds = $oldOrders->pluck('admin_id')->unique()->toArray();

            if (config('app.round_robin_order_receiving')) {
                $adminQ = Admin::orderByRaw('CASE WHEN is_active = 1 THEN 0 ELSE 1 END, role_id desc, last_order_received_at asc');
                $admin = count($adminIds) > 0 ? $adminQ->whereIn('id', $adminIds)->first() ?? $adminQ->first() : $adminQ->first();
            } else {
                $adminQ = Admin::where('role_id', Admin::SALESMAN)->where('is_active', true)->inRandomOrder();
                if (count($adminIds) > 0) {
                    $admin = $adminQ->whereIn('id', $adminIds)->first() ?? $adminQ->first() ?? Admin::where('is_active', true)->inRandomOrder()->first();
                } else {
                    $admin = $adminQ->first() ?? Admin::where('is_active', true)->inRandomOrder()->first();
                }
            }

            $orderData = [
                'courier' => 'Other',
                'is_fraud' => $oldOrders->whereIn('status', ['CANCELLED', 'RETURNED', 'PAID_RETURN'])->count() > 0,
                'is_repeat' => $oldOrders->count() > 0,
                'shipping_area' => $data['shipping'],
                'shipping_cost' => $this->shippingCost($data['shipping']),
                'retail_delivery_fee' => $this->retailDeliveryFee,
                'advanced' => $this->advanced,
                'retail_discount' => $this->retailDiscount,
                'subtotal' => cart()->subtotal(),
                'purchase_cost' => cart()->content()->sum(fn ($item): int|float => ($item->options->purchase_price ?: $item->options->price) * $item->qty),
            ];

            // Add city and area data if Pathao is enabled and user_selects_city_area is checked
            if ((setting('Pathao')->enabled ?? false) && (setting('Pathao')->user_selects_city_area ?? false)) {
                $orderData['city_id'] = $this->city_id;
                $orderData['area_id'] = $this->area_id;
                $orderData['courier'] = 'Pathao';
            }

            $data += [
                'source_id' => config('app.instant_order_forwarding') ? 0 : null,
                'admin_id' => $admin->id,
                'user_id' => $user->id, // If User Logged In
                'status' => $status,
                'status_at' => now()->toDateTimeString(),
                // Additional Data
                'data' => $orderData,
            ];

            $order = Order::create($data);

            defer(function () use ($admin, $user, $order): void {
                $admin->update(['last_order_received_at' => now()]);
                $user->notify(new OrderPlaced($order));

                deleteOrUpdateCart();

                Cache::add('fraud:hourly:'.request()->ip(), 0, now()->addHour());
                Cache::add('fraud:daily:'.request()->ip(), 0, now()->addDay());

                Cache::increment('fraud:hourly:'.request()->ip());
                Cache::increment('fraud:daily:'.request()->ip());

                Cache::add('fraud:hourly:'.$order->phone, 0, now()->addHour());
                Cache::add('fraud:daily:'.$order->phone, 0, now()->addDay());

                Cache::increment('fraud:hourly:'.$order->phone);
                Cache::increment('fraud:daily:'.$order->phone);
            });

            if (config('meta-pixel.meta_pixel')) {
                $this->facebookService->trackPurchase([
                    'id' => $order->id,
                    'total' => $order->data['subtotal'],
                ], $data['products'], [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone_number,
                    'external_id' => $user->id,
                ], $this);
            }

            return $order;
        });

        if (! $this->order instanceof \App\Models\Order) {
            return back();
        }

        // Undefined index email.
        // $data['email'] && Mail::to($data['email'])->queue(new OrderPlaced($order));

        if (config('app.instant_order_forwarding') && ! config('app.demo')) {
            dispatch(new \App\Jobs\CallOnindaOrderApi($this->order->id));
        }

        cart()->destroy();
        session()->flash('completed', 'Dear '.$data['name'].', Your Order is Successfully Received. Thanks For Your Order.');

        return to_route($this->getRedirectRoute(), [
            'order' => $this->order?->getKey(),
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
        // Create a temporary Order instance to use its Pathao methods
        $tempOrder = new \App\Models\Order;
        $this->cartUpdated();

        return view('livewire.checkout', [
            'user' => optional(auth('user')->user()),
            'pathaoCities' => collect($tempOrder->pathaoCityList()),
            'pathaoAreas' => collect($tempOrder->pathaoAreaList($this->city_id)),
            'retail' => $this->retail,
            'advanced' => $this->advanced,
            'retailDeliveryFee' => $this->retailDeliveryFee,
            'retailDiscount' => $this->retailDiscount,
        ]);
    }

    protected function fillFromCookie(): bool
    {
        return true;
    }

    protected function getRedirectRoute(): string
    {
        return 'thank-you';
    }

    protected function getDefaultStatus()
    {
        return data_get(config('app.orders', []), 0, 'PENDING'); // Default Status
    }

    public function toJSON(): array
    {
        return [
            'name' => $this->name,
            'phone' => $this->phone,
            'shipping' => $this->shipping,
            'address' => $this->address,
            'note' => $this->note,
            'city_id' => $this->city_id,
            'area_id' => $this->area_id,
            'retail' => $this->retail,
            'retailDeliveryFee' => $this->retailDeliveryFee,
            'advanced' => $this->advanced,
            'retailDiscount' => $this->retailDiscount,
        ];
    }
}
