<?php

namespace App\Livewire;

use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\User\OrderConfirmed;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class EditOrder extends Component
{
    private array $attrs = [
        'name', 'phone', 'email', 'address', 'note', 'status',
    ];

    private array $meta = [
        'discount', 'advanced', 'retail_discount', 'retail_delivery_fee', 'shipping_area', 'shipping_cost',
        'subtotal', 'courier', 'city_id', 'area_id', 'weight', 'packaging_charge',
        // 'area_name', 'is_fraud', 'is_repeat',
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

    #[Validate('required')]
    public string $status = 'CONFIRMED';

    // Meta Data
    #[Validate('numeric|min:0')]
    public $discount = 0;

    #[Validate('numeric|min:0')]
    public $advanced = 0;

    #[Validate('numeric|min:0')]
    public $retail_discount = 0;

    #[Validate('numeric|min:0')]
    public $retail_delivery_fee = 0;

    #[Validate('required')]
    public string $shipping_area = '';

    #[Validate('numeric|min:0')]
    public $shipping_cost = 0;

    #[Validate('numeric|min:0')]
    public $subtotal = 0;

    public string $courier = 'Other';

    public string $city_id = '';

    public string $area_id = '';

    #[Validate('numeric|min:0')]
    public $weight = 0.5;

    #[Validate('numeric|min:0')]
    public $packaging_charge = 25;

    public array $selectedProducts = [];

    public $search;

    public $options = [];

    public bool $activitiesLoaded = false;

    public Collection $activities;

    public bool $courierReportLoaded = false;

    public bool $cheapCourierReportLoaded = false;

    public function getCourierReportProperty()
    {
        $expires = config('services.courier_report.expires');
        if (! $expires || \Illuminate\Support\Facades\Date::parse($expires)->isPast()) {
            return 'API Expired';
        }

        $report = cacheMemo()->remember(
            'courier:'.($this->order->phone ?? ''),
            now()->addHours(4),
            function () {
                try {
                    return Http::retry(3, 100)
                        ->withToken(config('services.courier_report.key'))
                        ->post(config('services.courier_report.url'), [
                            'phone' => $this->order->phone ?? '',
                        ])
                        ->json();
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
            },
        );

        if (is_string($report)) {
            cacheMemo()->forget('courier:'.($this->order->phone ?? ''));
        }

        return $report;
    }

    protected function prepareForValidation($attributes): array
    {
        if (Str::startsWith($attributes['phone'], '01')) {
            $this->phone = $attributes['phone'] = '+88'.$attributes['phone'];
        }

        return $attributes;
    }

    public function mount(Order $order): void
    {
        $this->order = $order;
        $this->fill($this->order->only($this->attrs));
        $this->activities = collect();

        // Cast meta data to proper types
        $this->discount = (int) ($this->order->data['discount'] ?? 0);
        $this->advanced = (int) ($this->order->data['advanced'] ?? 0);
        $this->retail_discount = (int) ($this->order->data['retail_discount'] ?? 0);
        $this->retail_delivery_fee = (int) ($this->order->data['retail_delivery_fee'] ?? 0);
        $this->shipping_cost = (int) ($this->order->data['shipping_cost'] ?? 0);
        $this->subtotal = (int) ($this->order->data['subtotal'] ?? 0);
        $this->packaging_charge = (int) ($this->order->data['packaging_charge'] ?? 25);
        $this->weight = (float) ($this->order->data['weight'] ?? 0.5);

        // Handle string properties
        $this->shipping_area = $this->order->data['shipping_area'] ?? '';
        $this->courier = $this->order->data['courier'] ?? 'Other';
        $this->city_id = $this->order->data['city_id'] ?? '';
        $this->area_id = $this->order->data['area_id'] ?? '';

        foreach (json_decode(json_encode($this->order->products), true) ?? [] as $product) {
            $this->selectedProducts[$product['id']] = $product;

            $parentId = $product['parent_id'] ?? $product['id'];

            // Initialize options from selected products if they have selected_options
            if (isset($product['selected_options']) && is_array($product['selected_options'])) {
                $this->options[$parentId] = $product['selected_options'];
            } else {
                // If selected_options not stored, try to determine from the product
                // Load the product to check if it's a variation
                $productModel = Product::with('options')->find($product['id']);
                if ($productModel) {
                    // If it's a variation (has parent_id in database), get its options
                    if ($productModel->parent_id) {
                        $this->options[$productModel->parent_id] = $productModel->options->pluck('id', 'attribute_id')->toArray();
                    } elseif ($parentId != $product['id']) {
                        // If parent_id is set in order data but product is not a variation in DB
                        // This means the stored product ID might be a variation that was added
                        $parentProduct = Product::with('variations.options')->find($parentId);
                        if ($parentProduct && $parentProduct->variations->isNotEmpty()) {
                            // Try to find the variation that matches this product ID
                            $variation = $parentProduct->variations->firstWhere('id', $product['id']);
                            if ($variation) {
                                $this->options[$parentId] = $variation->options->pluck('id', 'attribute_id')->toArray();
                            }
                        }
                    }
                }
            }
        }
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

        // Store parent product ID and selected options for variation changes
        $productData['parent_id'] = $product->parent_id ?? $product->id;
        $productData['selected_options'] = $this->options[$product->id] ?? [];

        $this->selectedProducts[$id] = $productData;

        $this->updatedShippingArea($this->shipping_area);

        $this->search = '';
        $this->dispatch('notify', ['message' => 'Product added successfully.']);
    }

    public function updateSelectedProductVariation($productId, $attributeId, $optionId): void
    {
        if (! isset($this->selectedProducts[$productId])) {
            return;
        }

        $selectedProduct = $this->selectedProducts[$productId];
        $parentId = $selectedProduct['parent_id'] ?? $productId;

        // Get the parent product
        $parentProduct = Product::with('variations.options')->find($parentId);
        if (! $parentProduct) {
            return;
        }

        // Update options for this product (this will update the UI)
        if (! isset($this->options[$parentId])) {
            $this->options[$parentId] = [];
        }
        $this->options[$parentId][$attributeId] = (int) $optionId;

        // Find the matching variation
        $variation = $parentProduct->variations->first(function ($item) use ($parentId) {
            return $item->options
                ->pluck('id')
                ->diff($this->options[$parentId] ?? [])
                ->isEmpty();
        });

        // If no variation found, use parent product
        $product = $variation ?? $parentProduct;

        if ($product->should_track && $product->stock_count <= 0) {
            session()->flash('error', 'Selected variation is out of stock.');

            return;
        }

        // Update the selected product data
        $quantity = $selectedProduct['quantity'] ?? 1;
        $productData = (new ProductResource($product))->toCartItem($quantity);
        $productData['parent_id'] = $parentId;
        $productData['selected_options'] = $this->options[$parentId];

        // Preserve retail_price if it exists
        if (isset($selectedProduct['retail_price'])) {
            $productData['retail_price'] = $selectedProduct['retail_price'];
        }

        // Update the product in selectedProducts, keeping the same key
        // but updating the ID to the new variation's ID
        $newProductId = $product->id;
        if ($newProductId != $productId) {
            // If the ID changed, we need to update the key
            unset($this->selectedProducts[$productId]);
            $this->selectedProducts[$newProductId] = $productData;
        } else {
            $this->selectedProducts[$productId] = $productData;
        }

        $this->updatedShippingArea($this->shipping_area);
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
            'shipping_cost' => $this->order->getShippingCost($this->selectedProducts, $subtotal, $value),
        ]);
        if (isOninda() && ! config('app.resell')) {
            $this->fill(['retail_delivery_fee' => $this->shipping_cost]);
            $this->updatedRetailDeliveryFee($this->shipping_cost);
        }
    }

    public function updatedRetailDeliveryFee($value): void
    {
        $this->order->fill(['data' => array_merge($this->order->data, ['retail_delivery_fee' => $value])]);
    }

    public function updatedPackagingCharge($value): void
    {
        $this->order->fill(['data' => array_merge($this->order->data, ['packaging_charge' => $value])]);
    }

    public function updateOrder()
    {
        $this->validate();

        if (empty($this->selectedProducts)) {
            return session()->flash('error', 'Please add products to the order.');
        }

        if (isOninda() && ! config('app.resell')) {
            $this->fill(['retail_discount' => $this->discount]);
        }

        $this->order
            ->fill($this->only($this->attrs))
            ->fill(['data' => array_merge($this->only($this->meta), [
                'purchase_cost' => $this->order->getPurchaseCost($this->selectedProducts),
            ])])
            ->fill(['products' => $this->selectedProducts]);

        if ($this->order->exists) {
            $confirming = false;
            if ($this->order->status != $this->status) {
                $confirming = $this->status === 'CONFIRMED';
                $this->order->forceFill([
                    'status_at' => now()->toDateTimeString(),
                ]);
            }

            $this->order->save();

            if (config('app.instant_order_forwarding') && ! config('app.demo')) {
                dispatch(new \App\Jobs\CallOnindaOrderApi($this->order->id));
            }

            if ($confirming && ($user = $this->order->user)) {
                $user->notify(new OrderConfirmed($this->order));
            }

            session()->flash('success', 'Order updated successfully.');
        } else {
            $this->order->fill([
                'user_id' => $this->getUser()->id,
                'admin_id' => auth('admin')->id(),
                'type' => Order::MANUAL,
                'status_at' => now()->toDateTimeString(),
                'source_id' => config('app.instant_order_forwarding') ? 0 : null,
            ])->save();

            session()->flash('success', 'Order created successfully.');

            return to_route('admin.orders.edit', $this->order);
        }

        return to_route('admin.orders.edit', $this->order);
    }

    private function getUser()
    {
        if ($user = auth('user')->user()) {
            return $user;
        }

        // For Oninda environment, create a walk-in customer
        if (isOninda()) {
            return User::query()->firstOrCreate(
                ['phone_number' => '+8800000000000'],
                array_merge([
                    'name' => 'Walk-in Reseller',
                    'email' => 'walkin@hotash.tech',
                    'shop_name' => 'Walk-in Store',
                ], [
                    'email_verified_at' => now(),
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                    'remember_token' => Str::random(10),
                ])
            );
        }

        // For non-Oninda environment, create regular user
        return User::query()->firstOrCreate(
            ['phone_number' => $this->order->phone],
            array_merge([
                'name' => $this->order->name,
                'email' => $this->order->email,
            ], [
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ])
        );
    }

    public function loadActivities(): void
    {
        if ($this->activitiesLoaded) {
            return;
        }

        $this->activities = $this->order
            ->activities()
            ->with('causer')
            ->latest()
            ->get();

        $this->activitiesLoaded = true;
    }

    public function loadCourierReport(): void
    {
        $this->courierReportLoaded = true;
    }

    public function loadCheapCourierReport(): void
    {
        $this->cheapCourierReportLoaded = true;
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

        // Load parent products for selected products to show variations
        $selectedProductParents = collect();
        foreach ($this->selectedProducts as $selectedProduct) {
            $parentId = $selectedProduct['parent_id'] ?? $selectedProduct['id'];
            if (! $selectedProductParents->has($parentId)) {
                $parent = Product::with('variations.options')->find($parentId);
                if ($parent) {
                    $selectedProductParents[$parentId] = $parent;
                }
            }
        }

        return view('livewire.edit-order', [
            'products' => $products,
            'selectedProductParents' => $selectedProductParents,
        ]);
    }
}
