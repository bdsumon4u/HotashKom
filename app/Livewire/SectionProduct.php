<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class SectionProduct extends Component
{
    public string $search = '';

    public array $products = [];

    public array $selectedIds = [];

    public array $categoryIds = [];

    public function mount(array $selectedIds = []): void
    {
        $this->selectedIds = $selectedIds;
    }

    public function updatedSearch()
    {
        if (strlen($this->search) < 3) {
            return $this->products = [];
        }
        $this->products = Product::whereNull('parent_id')
            ->where(fn ($q) => $q->where('name', 'like', "%$this->search%")->orWhere('sku', $this->search))
            ->take(5)->get()->map(fn ($product, $i): array => [
                'order' => $i + 1,
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'image' => $product->base_image?->src,
            ])->toArray();
    }

    public function updateTaskOrder($data): void
    {
        $this->selectedIds = [];
        foreach ($data as $item) {
            $this->selectedIds[] = $item['value'];
        }
    }

    public function addProduct($id): void
    {
        $this->selectedIds[] = $id;

        $this->dispatch('notify', ['message' => 'Product added successfully.']);
    }

    public function removeProduct($id): void
    {
        $this->selectedIds = array_diff($this->selectedIds, [$id]);
    }

    public function render()
    {
        return view('livewire.section-product', [
            'selectedProducts' => Product::whereIn('id', $this->selectedIds)
                ->get()->mapWithKeys(fn ($product, $i): array => [$product->id => [
                    'order' => array_search($product->id, $this->selectedIds) + 1,
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'image' => $product->base_image?->src,
                ]])->sortBy('order')->toArray(),
        ]);
    }
}
