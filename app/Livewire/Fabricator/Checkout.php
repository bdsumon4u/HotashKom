<?php

namespace App\Livewire\Fabricator;

use App\Livewire\ProductDetail;
use App\Models\Product;

class Checkout extends \App\Livewire\Checkout
{
    public ?string $layout = null;

    public Product $product;

    public function increaseQuantity($id): void
    {
        if (! ($row = cart($id)) && ($product = Product::find($id))) {
            ProductDetail::landing($product);
        } elseif ($row->qty < $row->options->max || $row->options->max === -1) {
            cart()->update($row->rowId, $row->qty + 1);
        }
        $this->cartUpdated();
    }

    public function decreaseQuantity($id): void
    {
        if (! ($row = cart($id)) && ($product = Product::find($id))) {
            ProductDetail::landing($product);
        } elseif ($row->qty > 1) {
            cart()->update($row->rowId, $row->qty - 1);
        }
        $this->cartUpdated();
    }

    public function mount(): void
    {
        ProductDetail::landing($this->product);
        parent::mount();
    }

    public function render()
    {
        return view('livewire.fabricator.checkout');
    }
}
