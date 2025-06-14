<?php

namespace App\Livewire;

use App\Models\Attribute;
use App\Models\Product;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ProductDetail extends Component
{
    public Product $product;

    public Product $selectedVar;

    public array $options = [];

    public int $maxQuantity = 0;

    public int $quantity = 1;

    #[Validate('required|numeric|min:0')]
    public int $retailPrice = 0;

    public bool $showBrandCategory = false;

    public static function landing(Product $product): self
    {
        $component = new self;
        $component->product = $product;
        $component->mount();
        $component->addToCart('landing');

        return $component;
    }

    public function updatedOptions($value, $key): void
    {
        $variation = $this->product->variations->first(fn ($item) => $item->options->pluck('id')->diff($this->options)->isEmpty());

        if ($variation) {
            $this->selectedVar = $variation;
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

    public function addToCart($instance = 'default')
    {
        if (! auth('user')->check()) {
            $this->dispatch('notify', ['message' => 'Please login to add product to cart', 'type' => 'error']);

            return redirect()->route('user.login')->with('danger', 'Please login to add product to cart');
        }

        session(['kart' => $instance]);
        if ($instance == 'landing') {
            cart()->destroy();
        }
        cart()->instance($instance)->add(
            $this->selectedVar->id,
            $this->selectedVar->var_name,
            $quantity = min($this->quantity, $this->maxQuantity),
            $this->selectedVar->getPrice($quantity),
            [
                'parent_id' => $this->selectedVar->parent_id ?? $this->selectedVar->id,
                'slug' => $this->selectedVar->slug,
                'image' => optional($this->selectedVar->base_image)->path,
                'category' => $this->product->category,
                'max' => $this->maxQuantity,
                'retail_price' => $this->retailPrice,
                'shipping_inside' => $this->selectedVar->shipping_inside,
                'shipping_outside' => $this->selectedVar->shipping_outside,
            ],
        );

        storeOrUpdateCart();

        $this->dispatch('dataLayer', [
            'event' => 'add_to_cart',
            'ecommerce' => [
                'currency' => 'BDT',
                'value' => $this->selectedVar->getPrice($quantity),
                'items' => [
                    [
                        'item_id' => $this->selectedVar->id,
                        'item_name' => $this->selectedVar->name,
                        'item_category' => $this->product->category,
                        'price' => $this->selectedVar->getPrice($quantity),
                        'quantity' => $quantity,
                    ],
                ],
            ],
        ]);

        $this->dispatch('cartUpdated');
        $this->dispatch('notify', ['message' => 'Product added to cart']);

        if ($instance != 'default' && $instance != 'landing') {
            return redirect()->route('checkout');
        }
    }

    public function mount(): void
    {
        $maxPerProduct = setting('fraud')->max_qty_per_product ?? 3;
        if ($this->product->variations->isNotEmpty()) {
            $this->selectedVar = $this->product->variations->where('slug', request()->segment(2))->first()
                ?? $this->product->variations->random();
        } else {
            $this->selectedVar = $this->product;
            $this->showBrandCategory = true;
        }
        $this->options = $this->selectedVar->options->pluck('id', 'attribute_id')->toArray();
        $this->maxQuantity = $this->selectedVar->should_track ? min($this->selectedVar->stock_count, $maxPerProduct) : $maxPerProduct;
        $this->retailPrice = (int) ($this->selectedVar->selling_price * 1.25);
    }

    public function deliveryText($freeDelivery)
    {
        if ($freeDelivery->for_all ?? false) {
            $text = '<ul class="p-0 pl-4 mb-0 list-unstyled">';
            if ($freeDelivery->min_quantity > 0) {
                $text .= '<li>কমপক্ষে <strong class="text-danger">'.$freeDelivery->min_quantity.'</strong> টি প্রোডাক্ট অর্ডার করুন</li>';
            }
            if ($freeDelivery->min_amount > 0) {
                $text .= '<li>কমপক্ষে <strong class="text-danger">'.$freeDelivery->min_amount.'</strong> টাকার প্রোডাক্ট অর্ডার করুন</li>';
            }

            return $text.'</ul>';
        }

        if (array_key_exists($this->product->id, $products = ((array) ($freeDelivery->products ?? [])) ?? [])) {
            return 'কমপক্ষে <strong class="text-danger">'.$products[$this->product->id].'</strong> টি অর্ডার করুন';
        }

        return false;
    }

    public function render()
    {
        $optionGroup = $this->product->variations->pluck('options')->flatten()->unique('id')->groupBy('attribute_id');

        return view('livewire.product-detail', [
            'optionGroup' => $optionGroup,
            'attributes' => Attribute::find($optionGroup->keys()),
            'free_delivery' => $freeDelivery = setting('free_delivery'),
            'deliveryText' => $this->deliveryText($freeDelivery),
        ]);
    }
}
