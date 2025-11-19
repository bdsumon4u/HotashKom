<?php

namespace App\Livewire;

use App\Models\Attribute;
use App\Models\Product;
use App\Services\FacebookPixelService;
use App\Traits\HasCart;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ProductDetail extends Component
{
    use HasCart;

    public Product $product;

    public Product $selectedVar;

    public array $options = [];

    public int $maxQuantity = 0;

    public int $quantity = 1;

    #[Validate('required|numeric|min:0')]
    public int $retailPrice = 0;

    public bool $showBrandCategory = false;

    protected $facebookService;

    public function boot(FacebookPixelService $facebookService): void
    {
        $this->facebookService = $facebookService;
    }

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
            $this->dispatch('variantChanged', variantId: $variation->id, variantImageSrc: $variation->base_image ? asset($variation->base_image->src) : null);
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
        return $this->addToKart($this->selectedVar, $this->quantity, $instance, $this->retailPrice);
    }

    public function mount(): void
    {
        $this->facebookService = app(FacebookPixelService::class);
        $maxPerProduct = setting('fraud')->max_qty_per_product ?? 3;
        if ($this->product->variations->isNotEmpty()) {
            $segmentSlug = request()->segment(2);
            $this->selectedVar = $this->product->variations->where('slug', $segmentSlug ? rawurldecode((string) $segmentSlug) : null)->first()
                ?? $this->product->variations->random();
        } else {
            $this->selectedVar = $this->product;
            $this->showBrandCategory = true;
        }
        $this->options = $this->selectedVar->options->pluck('id', 'attribute_id')->toArray();
        $this->maxQuantity = $this->selectedVar->should_track ? min($this->selectedVar->stock_count, $maxPerProduct) : $maxPerProduct;
        $this->retailPrice = $this->selectedVar->retailPrice();
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
