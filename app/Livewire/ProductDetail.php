<?php

namespace App\Livewire;

use App\Models\Attribute;
use App\Models\Product;
use Livewire\Component;

class ProductDetail extends Component
{
    public Product $product;

    public Product $selectedVar;

    public array $options = [];

    public int $maxQuantity = 0;

    public int $quantity = 1;

    public bool $showBrandCategory = false;

    public function updatedOptions($value, $key)
    {
        $variation = $this->product->variations->first(fn($item) => $item->options->pluck('id')->diff($this->options)->isEmpty());

        if ($variation) {
            $this->selectedVar = $variation;
        }
    }

    public function increment()
    {
        if ($this->quantity < $this->maxQuantity) {
            $this->quantity++;
        }
    }

    public function decrement()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart()
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$this->selectedVar->id])) {
            $quantity = $cart[$this->selectedVar->id]['quantity'] = min($this->quantity, $this->maxQuantity);
            $cart[$this->selectedVar->id]['price'] = $this->selectedVar->getPrice($quantity);
        } else {
            $cart[$this->selectedVar->id] = [
                'id' => $this->selectedVar->id,
                'parent_id' => $this->selectedVar->parent_id ?? $this->selectedVar->id,
                'name' => $this->selectedVar->var_name,
                'slug' => $this->selectedVar->slug,
                'image' => optional($this->selectedVar->base_image)->path,
                'category' => $this->product->category,
                'quantity' => $quantity = min($this->quantity, $this->maxQuantity),
                'price' => $this->selectedVar->getPrice($quantity),
                'max' => $this->maxQuantity,
                'shipping_inside' => $this->selectedVar->shipping_inside,
                'shipping_outside' => $this->selectedVar->shipping_outside,
            ];
        }

        session()->put('cart', $cart);
        $product = $cart[$this->selectedVar->id];

        $this->dispatch('dataLayer', [
            'event' => 'add_to_cart',
            'ecommerce' => [
                'currency' => 'BDT',
                'value' => $product['price'] * $product['quantity'],
                'items' => [
                    [
                        'item_id' => $product['id'],
                        'item_name' => $product['name'],
                        'item_category' => $product['category'],
                        'price' => $product['price'],
                        'quantity' => $product['quantity'],
                    ],
                ],
            ],
        ]);

        $this->dispatch('cartUpdated');
        $this->dispatch('notify', ['message' => 'Product added to cart']);
    }

    public function orderNow()
    {
        $cart = session()->get('cart', []);
        $kart = session()->get('kart');
        if (isset($cart[$kart])) {
            unset($cart[$kart]);
        }
        session()->put('cart', $cart);

        $this->addToCart();

        session()->put('kart', $this->selectedVar->id);

        return redirect()->route('checkout');
    }

    public function mount()
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
            $text .= '</ul>';

            return $text;
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
