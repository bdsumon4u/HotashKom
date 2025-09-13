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

    public function boot(FacebookPixelService $facebookService)
    {
        $this->facebookService = $facebookService;
    }

    public function mount()
    {
        $this->facebookService = app(FacebookPixelService::class);
    }

    public function addToCart($instance = 'default')
    {
        return $this->addToKart($this->product, 1, $instance);
    }

    public function render()
    {
        return view('livewire.product-card');
    }
}
