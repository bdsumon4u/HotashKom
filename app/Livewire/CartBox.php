<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class CartBox extends Component
{
    public array $cart = [];

    public int $subtotal = 0;

    #[On('cartUpdated')]
    public function refresh(): void
    {
        $this->cart = session()->get('cart', []);
        $this->subtotal = collect($this->cart)->sum(fn ($item): int|float => $item['price'] * $item['quantity']);
    }

    public function mount(): void
    {
        $this->cart = session()->get('cart', []);
        $this->updatedCart();
    }

    public function remove($id): void
    {
        unset($this->cart[$id]);
        session()->put('cart', $this->cart);
        $this->updatedCart();
    }

    public function increaseQuantity($id): void
    {
        $this->cart[$id]['quantity']++;
        session()->put('cart', $this->cart);
        $this->updatedCart();
    }

    public function decreaseQuantity($id): void
    {
        if ($this->cart[$id]['quantity'] > 1) {
            $this->cart[$id]['quantity']--;
        } else {
            unset($this->cart[$id]);
        }
        session()->put('cart', $this->cart);
        $this->updatedCart();
    }

    public function updatedCart(): void
    {
        $this->subtotal = collect($this->cart)->sum(fn ($item): int|float => $item['price'] * $item['quantity']);

        $this->dispatch('cartBoxUpdated');
    }

    public function render()
    {
        return view('livewire.cart-box');
    }
}
