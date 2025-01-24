<?php

namespace App\Livewire\Fabricator;

use App\Livewire\ProductDetail;
use App\Models\Product;

class Checkout extends \App\Livewire\Checkout
{
    protected string $store = 'landing';
    public Product $product;

    public function increaseQuantity($id): void
    {
        if (!isset($this->cart[$id]) && ($product = Product::find($id))) {
            ProductDetail::landing($product);
            $this->refresh();
        } else if ($this->cart[$id]['quantity'] < $this->cart[$id]['max'] || $this->cart[$id]['max'] === -1) {
            $this->cart[$id]['quantity']++;
            session()->put($this->store, $this->cart);
            $this->cartUpdated();
        }
    }

    public function decreaseQuantity($id): void
    {
        if (!isset($this->cart[$id]) && ($product = Product::find($id))) {
            ProductDetail::landing($product);
            $this->refresh();
        } else if ($this->cart[$id]['quantity'] > 1) {
            $this->cart[$id]['quantity']--;
            session()->put($this->store, $this->cart);
            $this->cartUpdated();
        }
    }

    public function mount(): void
    {
        session()->forget($this->store);
        ProductDetail::landing($this->product);
        parent::mount();
    }

    public function render()
    {
        return view('livewire.fabricator.checkout');
    }
}
