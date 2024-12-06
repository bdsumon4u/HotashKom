<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        $livewire->cart = collect($data['cart'])->mapWithKeys(function ($item) {
            if (! $product = Product::find($item['id'])) {
                return null;
            }

            return [$product->id => [
                'id' => $product->id,
                'parent_id' => $product->parent_id ?? $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'image' => optional($product->base_image)->path,
                'category' => $product->category,
                'quantity' => $item['quantity'],
                'price' => $product->getPrice($item['quantity']),
            ]];
        })->filter()->toArray();
        $livewire->name = $request->input('name');
        $livewire->phone = $request->input('phone');
        $livewire->address = $request->input('address');
        $livewire->note = $request->input('note');
        $livewire->shipping = $request->input('shipping');

        $livewire->cartUpdated();
        if ($livewire->checkout() instanceof \Illuminate\Http\RedirectResponse) {
            if (session('error')) {
                return response()->json(['message' => session('error')], 422);
            }
        }

        return response()->json(['message' => 'Order placed successfully.', 'order' => $livewire->order]);
    }
}
