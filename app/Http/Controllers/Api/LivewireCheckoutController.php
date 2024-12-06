<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Livewire\Checkout;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LivewireCheckoutController extends Controller
{
    public function __invoke(Request $request)
    {
        if (! ($hidePrefix = setting('show_option')->hide_phone_prefix ?? false)) {
            if (Str::startsWith($request->phone, '01')) {
                $request->merge(['phone' => Str::after($request->phone, '0')]);
            }
        } elseif (Str::startsWith($request->phone, '01')) { // hide prefix
            $request->merge(['phone' => '+88'.$request->phone]);
        }

        $data = $request->validate([
            'name' => 'required',
            'phone' => $hidePrefix ? 'required|regex:/^\+8801\d{9}$/' : 'required|regex:/^1\d{9}$/',
            'address' => 'required',
            'note' => 'nullable',
            'shipping' => 'required',
        ]);

        $livewire = new Checkout();
        $livewire->mount();

        $livewire->cart = $request->input('cart', []);
        $livewire->name = $request->input('name');
        $livewire->phone = $request->input('phone');
        $livewire->address = $request->input('address');
        $livewire->note = $request->input('note');
        $livewire->shipping = $request->input('shipping');

        $livewire->checkout();

        return response()->json(['message' => 'Order placed successfully.']);
    }
}
