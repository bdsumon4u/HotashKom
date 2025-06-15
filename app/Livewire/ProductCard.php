<?php

namespace App\Livewire;

use App\Models\Product;
use App\Traits\HasCart;
use Livewire\Component;

class ProductCard extends Component
{
    use HasCart;

    public Product $product;

    public function addToCart($instance = 'default')
    {
        return $this->addToKart($this->product, 1, $instance);
    }

    public function render()
    {
        return view('livewire.product-card');
    }
}
