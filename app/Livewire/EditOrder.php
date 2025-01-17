<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\User\OrderConfirmed;
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
        'discount', 'advanced', 'shipping_area', 'shipping_cost',
        'subtotal', 'courier', 'city_id', 'area_id', 'weight',
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
    public int $discount = 0;

    public int $advanced = 0;

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

    public function getCourierReportProperty()
    {
        return cache()->remember('courier:'.($this->order->phone ?? ''), now()->addDay(), fn() => Http::withToken(config('services.courier_report.key'))
            ->post(config('services.courier_report.url'), [
                'phone' => $this->order->phone ?? '',
            ])
            ->json());
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
        $this->order = $order; // Initialize before access
        $this->fill($this->order->only($this->attrs) + $this->order->data);

        foreach (json_decode(json_encode($this->order->products), true) ?? [] as $product) {
            $this->selectedProducts[$product['id']] = $product;
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
        // Manage Stock
        if ($product->should_track) {
            if ($product->stock_count <= 0) {
                return session()->flash('error', 'Out of Stock.');
            }
            $quantity = $product->stock_count >= $quantity ? $quantity : $product->stock_count;
            $product->decrement('stock_count', $quantity);
        }

        $this->selectedProducts[$id] = [
            'id' => $id,
            'name' => $product->var_name,
            'slug' => $product->slug,
            'image' => optional($product->base_image)->src,
            'price' => $selling = $product->getPrice($quantity),
            'quantity' => $quantity,
            'total' => $quantity * $selling,
            'shipping_inside' => $product->shipping_inside,
            'shipping_outside' => $product->shipping_outside,
        ];

        $this->updatedShippingArea('');

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

        $this->updatedShippingArea('');
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

        $this->updatedShippingArea('');
    }

    public function updatedShippingArea($value): void
    {
        $shipping_cost = 0;
        if (! (setting('show_option')->productwise_delivery_charge ?? false)) {
            $shipping_cost = setting('delivery_charge')->{$this->shipping_area === 'Inside Dhaka' ? 'inside_dhaka' : 'outside_dhaka'} ?? config('services.shipping.'.$this->shipping_area, 0);
        } else {
            $shipping_cost = collect($this->selectedProducts)->sum(function ($item) {
                $default = setting('delivery_charge')->{$this->shipping_area === 'Inside Dhaka' ? 'inside_dhaka' : 'outside_dhaka'} ?? config('services.shipping.'.$this->shipping_area, 0);
                if ($this->shipping_area === 'Inside Dhaka') {
                    return ($item['shipping_inside'] ?? $default) * (setting('show_option')->quantitywise_delivery_charge ?? false ? $item['quantity'] : 1);
                } else {
                    return ($item['shipping_outside'] ?? $default) * (setting('show_option')->quantitywise_delivery_charge ?? false ? $item['quantity'] : 1);
                }
            });
        }

        $this->fill(['shipping_cost' => $shipping_cost, 'subtotal' => $this->order->getSubtotal($this->selectedProducts)]);
    }

    public function updateOrder()
    {
        $this->validate();

        if (empty($this->selectedProducts)) {
            return session()->flash('error', 'Please add products to the order.');
        }

        $this->order
            ->fill($this->only($this->attrs))
            ->fill(['data' => $this->only($this->meta)])
            ->fill(['products' => json_encode($this->selectedProducts, JSON_UNESCAPED_UNICODE)]);

        if ($this->order->exists) {
            $confirming = false;
            if ($this->order->status != $this->status) {
                $confirming = $this->status === 'CONFIRMED';
                $this->order->forceFill([
                    'status_at' => now()->toDateTimeString(),
                ]);
            }

            $this->order->save();

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
            ])->save();

            session()->flash('success', 'Order created successfully.');

            return redirect()->route('admin.orders.edit', $this->order);
        }

        return redirect()->route('admin.orders.edit', $this->order);
    }

    private function getUser()
    {
        if ($user = auth('user')->user()) {
            return $user;
        }

        // $user->notify(new AccountCreated());

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

        return view('livewire.edit-order', [
            'products' => $products,
        ]);
    }
}
