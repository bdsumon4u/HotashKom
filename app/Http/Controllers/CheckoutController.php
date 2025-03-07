<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\User\AccountCreated;
use App\Notifications\User\OrderPlaced;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\GoogleTagManager\GoogleTagManagerFacade;

class CheckoutController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(CheckoutRequest $request)
    {
        if ($request->isMethod('GET')) {
            //\LaravelFacebookPixel::createEvent('AddToCart', $parameters = []);

            GoogleTagManagerFacade::set([
                'event' => 'begin_checkout',
                'ecommerce' => [
                    'currency' => 'BDT',
                    'value' => cart()->subTotal(),
                    'items' => cart()->content()->map(fn ($product): array => [
                        'item_id' => $product->id,
                        'item_name' => $product->name,
                        'item_category' => $product->options->category,
                        'price' => $product->price,
                        'quantity' => $product->qty,
                    ]),
                ],
            ]);

            return view('checkout');
        }

        $data = $request->validated();
        $fraud = setting('fraud');

        if (Cache::has('fraud:hourly:'.$request->ip()) || Cache::has('fraud:daily:'.$data['phone'])) {
            abort_if(Cache::get('fraud:hourly:'.$request->ip()) >= ($fraud->allow_per_hour ?? 3), 429, 'You have reached the maximum limit of orders per hour.');
            abort_if(Cache::get('fraud:daily:'.$request->ip()) >= ($fraud->allow_per_day ?? 7), 429, 'You have reached the maximum limit of orders per day.');
        }

        $order = DB::transaction(function () use ($data, &$order) {
            $products = Product::find(array_keys($data['products']))
                ->map(function (Product $product) use ($data) {
                    $id = $product->id;
                    $quantity = $data['products'][$id];

                    if ($quantity <= 0) {
                        return null;
                    }
                    // Manage Stock
                    if ($product->should_track) {
                        if ($product->stock_count <= 0) {
                            // return null; // Allow overstock
                        }
                        $quantity = $product->stock_count >= $quantity ? $quantity : $product->stock_count;
                        $product->decrement('stock_count', $quantity);
                    }

                    // Needed Attributes
                    return [
                        'id' => $id,
                        'name' => $product->var_name,
                        'slug' => $product->slug,
                        'image' => optional($product->base_image)->src,
                        'price' => $selling = $product->getPrice($quantity),
                        'quantity' => $quantity,
                        'total' => $quantity * $selling,
                    ];
                })->filter(function ($product) {
                    return $product != null; // Only Available Products
                })->toArray();

            $data['products'] = json_encode($products, JSON_UNESCAPED_UNICODE);
            $user = $this->getUser($data);
            $oldOrders = $user->orders()->get();
            $status = ! auth('user')->user() ? 'PENDING' // PENDING
                : data_get(config('app.orders', []), 0, 'PENDING'); // Default Status

            $oldOrders = Order::select(['id', 'admin_id', 'status'])->where('phone', $data['phone'])->get();
            $adminIds = $oldOrders->pluck('admin_id')->unique()->toArray();
            $adminQ = Admin::where('role_id', Admin::SALESMAN)->where('is_active', true)->inRandomOrder();
            if (count($adminIds) > 0) {
                $data['admin_id'] = $adminQ->whereIn('id', $adminIds)->first()->id ?? $adminQ->first()->id ?? null;
            } else {
                $data['admin_id'] = $adminQ->first()->id ?? null;
            }

            $data += [
                'user_id' => $user->id, // If User Logged In
                'status' => $status,
                'status_at' => now()->toDateTimeString(),
                // Additional Data
                'data' => [
                    'is_fraud' => $oldOrders->whereIn('status', ['CANCELLED', 'RETURNED'])->count() > 0,
                    'is_repeat' => $oldOrders->count() > 0,
                    'shipping_area' => $data['shipping'],
                    'shipping_cost' => setting('delivery_charge')->{$data['shipping'] == 'Inside Dhaka' ? 'inside_dhaka' : 'outside_dhaka'} ?? config('services.shipping.'.$data['shipping']),
                    'subtotal' => is_array($products) ? array_reduce($products, fn ($sum, $product) => $sum += $product['total']) : $products->sum('total'),
                ],
            ];

            // \LaravelFacebookPixel::createEvent('Purchase', ['currency' => 'USD', 'value' => data_get(json_decode($data['data'], true), 'subtotal')]);

            $order = Order::create($data);
            $user->notify(new OrderPlaced($order));

            return $order;
        });

        Cache::put('fraud:hourly:'.$request->ip(), Cache::get('fraud:hourly:'.$request->ip(), 0) + 1, now()->addHour());
        Cache::put('fraud:daily:'.$request->ip(), Cache::get('fraud:daily:'.$request->ip(), 0) + 1, now()->addDay());

        // Undefined index email.
        // $data['email'] && Mail::to($data['email'])->queue(new OrderPlaced($order));

        session()->flash('completed', 'Dear '.$data['name'].', Your Order is Successfully Recieved. Thanks For Your Order.');

        return redirect()->route('track-order', [
            'order' => optional($order)->getKey(),
            'clear' => 'all',
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
}
