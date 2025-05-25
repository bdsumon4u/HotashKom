<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\FacebookPixelService;
use Livewire\Component;

class ProductCard extends Component
{
    public Product $product;

    protected $facebookService;

    public function boot(FacebookPixelService $facebookService)
    {
        $this->facebookService = $facebookService;
    }

    public function addToCart($instance = 'default')
    {
        session(['kart' => $instance]);
        $fraudQuantity = setting('fraud')->max_qty_per_product ?? 3;

        cart()->add(
            $this->product->id,
            $this->product->name,
            1,
            $this->product->selling_price,
            [
                'parent_id' => $this->product->parent_id ?? $this->product->id,
                'slug' => $this->product->slug,
                'image' => optional($this->product->base_image)->path,
                'category' => $this->product->category,
                'max' => $this->product->should_track ? min($this->product->stock_count, $fraudQuantity) : $fraudQuantity,
                'shipping_inside' => $this->product->shipping_inside,
                'shipping_outside' => $this->product->shipping_outside,
            ],
        );

        storeOrUpdateCart();

        if (config('meta-pixel.meta_pixel')) {
            $this->facebookService->trackAddToCart([
                'id' => $this->product->id,
                'name' => $this->product->name,
                'price' => $this->product->selling_price,
                'page_url' => route('products.show', $this->product->slug),
            ], $this);
        }

        $this->dispatch('cartUpdated');
        $this->dispatch('notify', ['message' => 'Product added to cart']);

        if ($instance != 'default') {
            return redirect()->route('checkout');
        }
    }

    public function render()
    {
        return view('livewire.product-card');
    }
}
