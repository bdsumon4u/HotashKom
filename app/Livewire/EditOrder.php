<?php

namespace App\Livewire;

use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\User\OrderConfirmed;
use Illuminate\Support\Carbon;
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

    public int $retail_discount = 0;

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
        $expires = config('services.courier_report.expires');
        if (! $expires || Carbon::parse($expires)->isPast()) {
            return "API Expired";
        }

        $report = cache()->remember(
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
            cache()->forget('courier:'.($this->order->phone ?? ''));
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
            'shipping_cost' => $this->order->getShippingCost($this->selectedProducts, $subtotal, $value),
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
            ->fill(['data' => $this->only($this->meta)])
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
