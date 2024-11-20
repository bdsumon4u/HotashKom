<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Notifications\User\OrderPlaced;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
