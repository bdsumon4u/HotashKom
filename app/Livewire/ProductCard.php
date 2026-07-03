<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\FacebookPixelService;
use App\Traits\HasCart;
use Livewire\Component;

class ProductCard extends Component
{
    use HasCart;

    public Product $product;

    protected $facebookService;

    public function boot(FacebookPixelService $facebookService): void
    {
        $this->facebookService = $facebookService;
    }

    public function mount(): void
    {
        $this->facebookService = app(FacebookPixelService::class);
    }

    public function addToCart($instance = 'default')
    {
        return $this->addToKart($this->product, 1, $instance);
    }

    public function render()
    {
        $freeDelivery = setting('free_delivery');
        $isFreeDelivery = $this->isFreeDeliveryProduct($this->product, $freeDelivery);

        return view('livewire.product-card', [
            'free_delivery' => $freeDelivery,
            'is_free_delivery' => $isFreeDelivery,
        ]);
    }

    protected function isFreeDeliveryProduct(Product $product, mixed $freeDelivery): bool
    {
        if (! ($freeDelivery->enabled ?? false)) {
            return false;
        }

        if ($freeDelivery->for_all ?? false) {
            return true;
        }

        $products = (array) ($freeDelivery->products ?? []);

        return array_key_exists($product->id, $products);
    }
}
