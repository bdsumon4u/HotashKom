<?php

namespace App\Livewire\EditOrder;

use App\Models\Order;
use Illuminate\Support\Collection;
use Livewire\Component;

class Activities extends Component
{
    public Order $order;

    public bool $loaded = false;

    public Collection $activities;

    public function mount(Order $order): void
    {
        $this->order = $order;
        $this->activities = collect();
    }

    public function load(): void
    {
        if ($this->loaded) {
            return;
        }

        $this->activities = cacheMemo()->remember(
            'order_activities:'.$this->order->id,
            now()->addMinutes(2),
            fn () => $this->order
                ->activities()
                ->with('causer')
                ->latest()
                ->get()
        );

        $this->loaded = true;
    }

    public function render()
    {
        return view('livewire.edit-order.activities');
    }
}
