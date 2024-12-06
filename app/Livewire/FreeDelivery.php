<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class FreeDelivery extends Component
{
    public array $selectedProducts = [];

    public array $delivery_charge;

    public int $free_delivery;

    public int $free_for_all;

    public int $min_quantity;

    public int $min_amount;

    public $search;

    public function mount($freeDelivery, $deliveryCharge)
    {
        $freeDelivery = optional($freeDelivery);
        $this->free_delivery = (int)$freeDelivery->enabled ?? 0;
        $this->free_for_all = (int)$freeDelivery->for_all ?? 0;
        $this->min_quantity = $freeDelivery->min_quantity ?? 1;
        $this->min_amount = $freeDelivery->min_amount ?? 1;

        $products = ((array) ($freeDelivery->products ?? [])) ?? [];
        Product::find(array_keys($products))->each(function ($product) use ($products) {
            $this->addProduct($product, $products[$product->id], true);
        });
        $this->delivery_charge = json_decode(json_encode($deliveryCharge), true);
    }

    public function addProduct(Product $product, $quantity = 1, $silent = false)
    {
        foreach ($this->selectedProducts as $orderedProduct) {
            if ($orderedProduct['id'] === $product->id) {
                return session()->flash('error', 'Product already added.');
            }
        }

        $this->selectedProducts[$product->id] = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'image' => optional($product->base_image)->src,
            'quantity' => $quantity,
        ];

        if (! $silent) {
            $this->dispatch('notify', ['message' => 'Product added successfully.']);
        }
    }

    public function increaseQuantity($id)
    {
        $this->selectedProducts[$id]['quantity']++;
    }

    public function decreaseQuantity($id)
    {
        if ($this->selectedProducts[$id]['quantity'] > 1) {
            $this->selectedProducts[$id]['quantity']--;
        } else {
            unset($this->selectedProducts[$id]);
        }
    }

    public function render()
    {
        $products = collect();
        if (strlen($this->search) > 2) {
            $products = Product::where(fn ($q) => $q->where('name', 'like', "%$this->search%")->orWhere('sku', $this->search))
                ->whereNotIn('id', array_keys($this->selectedProducts))
                ->whereNull('parent_id')
                ->whereIsActive(1)
                ->take(5)
                ->get();
        }

        return view('livewire.free-delivery', [
            'products' => $products,
        ]);
    }
}
