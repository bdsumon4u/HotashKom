<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
final class OrderRelated extends Component
{
    public int $orderId;

    public Collection $otherOrders;

    public function mount(int $orderId): void
    {
        $this->orderId = $orderId;

        $order = Order::find($orderId);

        if (! $order) {
            $this->otherOrders = collect();

            return;
        }

        $this->otherOrders = Order::with('admin')
            ->where('phone', $order->phone)
            ->where('id', '!=', $order->id)
            ->orderByDesc('id')
            ->limit(50)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.order-related');
    }
}
