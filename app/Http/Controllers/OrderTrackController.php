<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Notifications\User\OrderPlaced;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\GoogleTagManager\GoogleTagManagerFacade;

class OrderTrackController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if (! $request->has('order')) {
            return view('track-order');
        }
        $order = Order::where(['id' => $request->order])->first();
        if (! $order instanceof Order) {
            return back()->withDanger('Invalid Tracking Info Or Order Record Was Deleted.');
        }

        if (session()->has('completed') && $request->is('thank-you')) {

            if (GoogleTagManagerFacade::isEnabled()) {
                $index = 0;
                GoogleTagManagerFacade::set([
                    'event' => 'purchase',
                    'ecommerce' => [
                        'transaction_id' => $order->id,
                        'affiliation' => $request->getHost(),
                        'value' => $order->data['subtotal'],
                        'tax' => 0,
                        'shipping' => $order->data['shipping_cost'],
                        'currency' => 'BDT',
                        'coupon' => '',
                        'items' => array_values(array_map(fn ($product): array => [
                            'item_id' => $product->id,
                            'item_name' => $product->name,
                            'affiliation' => $request->getHost(), // The store or affiliation (optional)
                            'coupon' => '', // Any coupon code applied to the item
                            'currency' => 'BDT',
                            'discount' => 0, // Any discount applied to the item
                            'index' => $index++, // The item's index in the list
                            // 'item_brand' => $product->brand,
                            'item_category' => $product->category,
                            'location_id' => 'BD', // The location associated with the item (optional)
                            'price' => $product->price,
                            'quantity' => $product->quantity,
                        ], (array) $order->products)),
                    ],
                    'customer' => [
                        'name' => $order->name,
                        'email' => $order->email,
                        'address' => $order->address,
                        'country' => 'Bangladesh',
                        'state' => 'N/A',
                        'city' => 'N/A',
                        'postal_code' => 'N/A',
                        'phone' => $order->phone,
                        'user_id' => $order->user_id,
                        'first_name' => explode(' ', $order->name, 2)[0] ?? '',
                        'last_name' => explode(' ', $order->name, 2)[1] ?? '',
                    ],
                ]);
            }

        }

        if ($request->isMethod('GET')) {
            return view('order-status', compact('order'));
        }

        if ($order->status != 'PENDING') {
            return back()->withDanger('Order is already confirmed.');
        }
        if ($request->get('action') === 'resend') {
            if (Cache::get('order:confirm:'.$order->id)) {
                return back()->withSuccess('Please wait for the confirmation code');
            } else {
                $order->user->notify(new OrderPlaced($order));

                return back()->withSuccess('Confirmation code has been sent through sms');
            }
        }
        if ($request->get('action') === 'confirm') {
            if (Cache::get('order:confirm:'.$order->id) == $request->get('code')) {
                $order->update(['status' => data_get(config('app.orders'), 0, 'PROCESSING')]);

                return back()->withSuccess('Your order has been confirmed');
            } else {
                return back()->withDanger('Incorrect confirmation code');
            }
        }
    }
}
