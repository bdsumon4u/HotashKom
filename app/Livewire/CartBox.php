<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class CartBox extends Component
{
    public function remove($id): void
    {
        cart()->remove($id);
        $this->dispatch('cartBoxUpdated');
    }

    #[On('cartUpdated')]
    public function render()
    {
        return view('livewire.cart-box');
    }
}
