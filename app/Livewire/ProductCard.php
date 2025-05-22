<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\FacebookConversionService;
use Livewire\Component;

class ProductCard extends Component
{
    public Product $product;
    protected $facebookService;

    public function boot(FacebookConversionService $facebookService)
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

        // Track AddToCart event with Facebook Conversion API
        if ($this->facebookService && $this->facebookService->isEnabled()) {
            $this->facebookService->trackEvent('AddToCart', [
                'client_ip_address' => request()->ip(),
                'client_user_agent' => request()->userAgent(),
            ], [
                'currency' => 'BDT',
                'value' => $this->product->selling_price,
                'content_ids' => [$this->product->id],
                'content_name' => $this->product->name,
            ]);
        }

        $this->dispatch('dataLayer', [
            'event' => 'add_to_cart',
            'ecommerce' => [
                'currency' => 'BDT',
                'value' => $this->product->selling_price,
                'items' => [
                    [
                        'item_id' => $this->product->id,
                        'item_name' => $this->product->name,
                        'item_category' => $this->product->category,
                        'price' => $this->product->selling_price,
                        'quantity' => 1,
                    ],
                ],
            ],
        ]);

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
