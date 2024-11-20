<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductCard extends Component
{
    public Product $product;

    public function addToCart()
    {
        $cart = session()->get('cart', []);
        $fraudQuantity = setting('fraud')->max_qty_per_product ?? 3;

        if (! isset($cart[$this->product->id])) {
            $cart[$this->product->id] = [
                'id' => $this->product->id,
                'parent_id' => $this->product->parent_id ?? $this->product->id,
                'name' => $this->product->name,
                'slug' => $this->product->slug,
                'image' => optional($this->product->base_image)->path,
                'category' => $this->product->category,
                'quantity' => 1,
                'price' => $this->product->selling_price,
                'max' => $this->product->should_track ? min($this->product->stock_count, $fraudQuantity) : $fraudQuantity,
                'shipping_inside' => $this->product->shipping_inside,
                'shipping_outside' => $this->product->shipping_outside,
            ];
        }

        session()->put('cart', $cart);
        $product = $cart[$this->product->id];

        $this->dispatch('dataLayer', [
            'event' => 'add_to_cart',
            'ecommerce' => [
                'currency' => 'BDT',
                'value' => $product['price'] * $product['quantity'],
                'items' => [
                    [
                        'item_id' => $product['id'],
                        'item_name' => $product['name'],
                        'item_category' => $product['category'],
                        'price' => $product['price'],
                        'quantity' => $product['quantity'],
                    ],
                ],
            ],
        ]);

        $this->dispatch('cartUpdated');
        $this->dispatch('notify', ['message' => 'Product added to cart']);
    }

    public function orderNow()
    {
        $cart = session()->get('cart', []);
        $kart = session()->get('kart');
        if (isset($cart[$kart])) {
            unset($cart[$kart]);
        }
        session()->put('cart', $cart);

        $this->addToCart();

        session()->put('kart', $this->product->id);

        return redirect()->route('checkout');
    }

    public function render()
    {
        return view('livewire.product-card');
    }
}
