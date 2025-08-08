<?php

namespace App\Livewire;

use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ResellerEditOrder extends Component
{
    private array $attrs = [
        'name', 'phone', 'email', 'address', 'note',
    ];

    private array $meta = [
        'discount', 'advanced', 'retail_discount', 'retail_delivery_fee', 'shipping_area', 'shipping_cost',
        'subtotal', 'courier', 'city_id', 'area_id', 'weight',
    ];

    public Order $order;

    public int $counter = 5;

    #[Validate('required')]
    public ?string $name = '';

    #[Validate('required|regex:/^\+8801\d{9}$/')]
    public ?string $phone = '';

    public ?string $email = '';

    #[Validate('required')]
    public ?string $address = '';

    public ?string $note = '';

    // Meta Data
    public int $discount = 0;

    public int $advanced = 0;

    public int $retail_discount = 0;

    public int $retail_delivery_fee = 0;

    #[Validate('required')]
    public string $shipping_area = '';

    public int $shipping_cost = 0;

    public int $subtotal = 0;

    public string $courier = 'Other';

    public string $city_id = '';

    public string $area_id = '';

    #[Validate('numeric')]
    public float $weight = 0.5;

    public array $selectedProducts = [];

    public $search;

    public $options = [];

    public bool $canCancel = false;

    protected function prepareForValidation($attributes): array
    {
        if (Str::startsWith($attributes['phone'], '01')) {
            $this->phone = $attributes['phone'] = '+88'.$attributes['phone'];
        }

        return $attributes;
    }

    public function mount(Order $order): void
    {
        // Check if reseller can edit this order
        $user = auth('user')->user();

        if ($order->user_id !== $user->id) {
            abort(403, 'You can only edit your own orders.');
        }

        // Check if order status allows editing
        if (! in_array($order->status, ['PENDING', 'CONFIRMED'])) {
            abort(403, 'You can only edit orders with PENDING or CONFIRMED status.');
        }

        $this->order = $order;
        $this->fill($this->order->only($this->attrs) + $this->order->data);

        foreach (json_decode(json_encode($this->order->products), true) ?? [] as $product) {
            $this->selectedProducts[$product['id']] = $product;
        }

        // Set canCancel property
        $this->canCancel = in_array($this->order->status, ['PENDING', 'CONFIRMED']);
    }

    public function addProduct(Product $product)
    {
        foreach ($this->selectedProducts as $orderedProduct) {
            if ($orderedProduct['id'] === $product->id) {
                return session()->flash('error', 'Product already added.');
            }
        }

        $quantity = 1;
        $id = $product->id;

        if ($product->should_track && $product->stock_count <= 0) {
            return session()->flash('error', 'Out of Stock.');
        }

        $productData = (new ProductResource($product))->toCartItem($quantity);

        $this->selectedProducts[$id] = $productData;

        $this->updatedShippingArea($this->shipping_area);

        $this->search = '';
        $this->dispatch('notify', ['message' => 'Product added successfully.']);
    }

    public function increaseQuantity($id): void
    {
        if (! isset($this->selectedProducts[$id])) {
            return;
        }
        $this->selectedProducts[$id]['quantity']++;
        $this->selectedProducts[$id]['total'] = $this->selectedProducts[$id]['quantity'] * $this->selectedProducts[$id]['price'];

        $this->updatedShippingArea($this->shipping_area);
    }

    public function decreaseQuantity($id): void
    {
        if (! isset($this->selectedProducts[$id])) {
            return;
        }
        if ($this->selectedProducts[$id]['quantity'] > 1) {
            $this->selectedProducts[$id]['quantity']--;
            $this->selectedProducts[$id]['total'] = $this->selectedProducts[$id]['quantity'] * $this->selectedProducts[$id]['price'];
        } else {
            unset($this->selectedProducts[$id]);
        }

        $this->updatedShippingArea($this->shipping_area);
    }

    public function updatedShippingArea($value): void
    {
        $this->fill([
            'subtotal' => $subtotal = $this->order->getSubtotal($this->selectedProducts),
            'retail_delivery_fee' => $this->order->getShippingCost($this->selectedProducts, $subtotal, $value),
        ]);
    }

    public function updateOrder()
    {
        $this->validate();

        if (empty($this->selectedProducts)) {
            return session()->flash('error', 'Please add products to the order.');
        }

        $this->order
            ->fill($this->only($this->attrs))
            ->fill(['data' => array_merge($this->only($this->meta), [
                'purchase_cost' => $this->order->getPurchaseCost($this->selectedProducts),
            ])])
            ->fill(['products' => $this->selectedProducts]);

        $this->order->save();

        session()->flash('success', 'Order updated successfully.');

        return redirect()->route('reseller.orders.show', $this->order);
    }

    public function cancelOrder()
    {
        // Check if reseller can cancel this order
        $user = auth('user')->user();

        if ($this->order->user_id !== $user->id) {
            return session()->flash('error', 'You can only cancel your own orders.');
        }

        // Check if order status allows cancellation
        if (! in_array($this->order->status, ['PENDING', 'CONFIRMED'])) {
            return session()->flash('error', 'You can only cancel orders with PENDING or CONFIRMED status.');
        }

        // Update order status to CANCELLED
        $this->order->update([
            'status' => 'CANCELLED',
            'status_at' => now()->toDateTimeString(),
        ]);

        session()->flash('success', 'Order cancelled successfully.');

        return redirect()->route('reseller.orders.show', $this->order);
    }

    public function render()
    {
        $products = collect();
        if (strlen((string) $this->search) > 2) {
            $products = Product::with('variations.options')
                ->whereNotIn('id', array_keys($this->selectedProducts))
                ->where(fn ($q) => $q->where('name', 'like', "%$this->search%")->orWhere('sku', $this->search))
                ->whereNull('parent_id')
                ->whereIsActive(1)
                ->take(5)
                ->get();

            foreach ($products as $product) {
                if ($product->variations->isNotEmpty() && ! isset($this->options[$product->id])) {
                    $this->options[$product->id] = $product->variations->random()->options->pluck('id', 'attribute_id');
                }
            }
        }

        return view('livewire.reseller-edit-order', [
            'products' => $products,
        ]);
    }
}
