<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\FacebookPixelService;
use App\Traits\HasCart;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ResellerProductCard extends Component
{
    use HasCart;

    public Product $product;

    public int $quantity = 1;

    #[Validate('required|numeric|min:0')]
    public int $retailPrice = 0;

    public bool $showOptions = false;

    public array $options = [];

    public int $maxQuantity = 0;

    protected $facebookService;

    public function boot(FacebookPixelService $facebookService)
    {
        $this->facebookService = $facebookService;
    }

    public function mount(): void
    {
        $this->facebookService ??= app(FacebookPixelService::class);
        $maxPerProduct = setting('fraud')->max_qty_per_product ?? 3;
        $this->maxQuantity = $this->product->should_track ? min($this->product->stock_count, $maxPerProduct) : $maxPerProduct;
        $this->retailPrice = $this->product->retailPrice();

        // Set default options if product has variations
        if ($this->product->variations->isNotEmpty()) {
            $this->options = $this->product->variations->random()->options->pluck('id', 'attribute_id')->toArray();
        }
    }

    public function toggleOptions(): void
    {
        $this->showOptions = ! $this->showOptions;
    }

    public function updatedOptions($value, $key): void
    {
        $variation = $this->product->variations->first(fn ($item) => $item->options->pluck('id')->diff($this->options)->isEmpty());

        if ($variation) {
            $this->product = $variation;
            $this->maxQuantity = $this->product->should_track ? min($this->product->stock_count, setting('fraud')->max_qty_per_product ?? 3) : (setting('fraud')->max_qty_per_product ?? 3);
            $this->retailPrice = $this->product->retailPrice();
        }
    }

    public function increment(): void
    {
        if ($this->quantity < $this->maxQuantity) {
            $this->quantity++;
        }
    }

    public function decrement(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(): void
    {
        // Check if user is verified
        $user = auth('user')->user();
        if (isOninda() && (! $user || ! $user->is_verified)) {
            $this->dispatch('notify', ['message' => 'Please verify your account to add products to cart', 'type' => 'error']);

            return;
        }

        $this->validate();

        if ($this->retailPrice <= 0) {
            $this->dispatch('notify', ['message' => 'Please set a valid retail price', 'type' => 'error']);

            return;
        }

        $this->addToKart($this->product, $this->quantity, 'default', $this->retailPrice);
    }

    public function render()
    {
        $optionGroup = $this->product->variations->pluck('options')->flatten()->unique('id')->groupBy('attribute_id');
        $attributes = \App\Models\Attribute::find($optionGroup->keys());

        return view('livewire.reseller-product-card', [
            'optionGroup' => $optionGroup,
            'attributes' => $attributes,
        ]);
    }
}
