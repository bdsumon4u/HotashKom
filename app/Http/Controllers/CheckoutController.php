<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\User;
use App\Notifications\User\AccountCreated;
use Illuminate\Support\Arr;
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
