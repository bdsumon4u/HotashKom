<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Livewire\Checkout;
use App\Models\Product;
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
            'cart' => 'required|array',
        ]);

        $livewire = new Checkout;
        $livewire->mount();

        // Clear existing cart
        cart()->destroy();

        // Add items to cart
        foreach ($data['cart'] as $item) {
            if ($product = Product::find($item['id'])) {
                $cartItem = (new ProductResource($product))->toCartItem($item['quantity']);
                cart()->add([
                    'id' => $cartItem['id'],
                    'name' => $cartItem['name'],
                    'qty' => $cartItem['quantity'],
                    'price' => $cartItem['price'],
                    'options' => [
                        'parent_id' => $cartItem['parent_id'],
                        'slug' => $cartItem['slug'],
                        'image' => $cartItem['image'],
                        'category' => $cartItem['category'],
                        'shipping_inside' => $cartItem['shipping_inside'],
                        'shipping_outside' => $cartItem['shipping_outside'],
                    ],
                ]);
            }
        }

        $livewire->name = $request->input('name');
        $livewire->phone = $request->input('phone');
        $livewire->address = $request->input('address');
        $livewire->note = $request->input('note');
        $livewire->shipping = $request->input('shipping');

        $livewire->cartUpdated();
        if ($livewire->checkout() instanceof \Illuminate\Http\RedirectResponse && session('error')) {
            return response()->json(['message' => session('error')], 422);
        }

        return response()->json(['message' => 'Order placed successfully.', 'order' => $livewire->order]);
    }
}
