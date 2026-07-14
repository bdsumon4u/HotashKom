<?php

use App\Livewire\Checkout;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// Expose the protected resolvePackagingCharge() through an anonymous subclass
function checkoutResolve(array $productIds): int
{
    $checkout = new class extends Checkout
    {
        public function callResolve(array $ids): int
        {
            // Mirrors how checkout() builds the $products array (keyed by id)
            $products = array_fill_keys($ids, []);

            return $this->resolvePackagingCharge($products);
        }
    };

    return $checkout->callResolve($productIds);
}

it('falls back to config default when no products have a packaging charge set', function () {
    config(['app.oninda' => true, 'app.resell' => true, 'app.packaging_charge' => 25]);

    $p1 = Product::factory()->create(['packaging_charge' => null]);
    $p2 = Product::factory()->create(['packaging_charge' => null]);

    expect(checkoutResolve([$p1->id, $p2->id]))->toBe(25);
});

it('uses a single product packaging charge over the config default', function () {
    config(['app.oninda' => true, 'app.resell' => true, 'app.packaging_charge' => 25]);

    $product = Product::factory()->create(['packaging_charge' => 40]);

    expect(checkoutResolve([$product->id]))->toBe(40);
});

it('uses the maximum packaging charge across multiple products', function () {
    config(['app.oninda' => true, 'app.resell' => true, 'app.packaging_charge' => 25]);

    $cheap = Product::factory()->create(['packaging_charge' => 30]);
    $mid = Product::factory()->create(['packaging_charge' => 50]);
    $pricey = Product::factory()->create(['packaging_charge' => 70]);

    expect(checkoutResolve([$cheap->id, $mid->id, $pricey->id]))->toBe(70);
});

it('uses max of set charge and fallback charge when one product has no charge set', function () {
    config(['app.oninda' => true, 'app.resell' => true, 'app.packaging_charge' => 25]);

    $p1 = Product::factory()->create(['packaging_charge' => 15]);
    $p2 = Product::factory()->create(['packaging_charge' => null]); // falls back to 25

    expect(checkoutResolve([$p1->id, $p2->id]))->toBe(25);
});

it('resolves variation packaging charge from parent product when variation own charge is null', function () {
    config(['app.oninda' => true, 'app.resell' => true, 'app.packaging_charge' => 25]);

    $parent = Product::factory()->create(['packaging_charge' => 15]);
    $child = Product::factory()->create(['parent_id' => $parent->id, 'packaging_charge' => null]);

    expect(checkoutResolve([$child->id]))->toBe(15);
});

it('returns config default when not in resell mode regardless of product charges', function () {
    config(['app.oninda' => true, 'app.resell' => false, 'app.packaging_charge' => 25]);

    $product = Product::factory()->create(['packaging_charge' => 80]);

    expect(checkoutResolve([$product->id]))->toBe(25);
});

it('resolves product packaging charge even when isOninda is false as long as resell mode is enabled', function () {
    config(['app.oninda' => false, 'app.resell' => true, 'app.packaging_charge' => 25]);

    $product = Product::factory()->create(['packaging_charge' => 15]);

    expect(checkoutResolve([$product->id]))->toBe(15);
});

it('respects a custom PACKAGING_CHARGE env value as default', function () {
    config(['app.oninda' => true, 'app.resell' => true, 'app.packaging_charge' => 50]);

    $product = Product::factory()->create(['packaging_charge' => null]);

    expect(checkoutResolve([$product->id]))->toBe(50);
});

it('updates packaging charge dynamically in EditOrder when products change', function () {
    config(['app.oninda' => true, 'app.resell' => true, 'app.packaging_charge' => 25]);

    $user = User::create([
        'name' => 'John Doe Reseller',
        'email' => 'john@example.com',
        'phone_number' => '+8801700000000',
        'password' => bcrypt('password'),
    ]);

    $order = Order::create([
        'user_id' => $user->id,
        'name' => 'John Doe',
        'phone' => '+8801700000000',
        'address' => 'Dhaka',
        'status' => 'PENDING',
        'status_at' => now()->toDateTimeString(),
        'data' => [
            'packaging_charge' => 25,
            'subtotal' => 0,
            'shipping_cost' => 0,
            'retail_delivery_fee' => 0,
            'advanced' => 0,
            'discount' => 0,
            'retail_discount' => 0,
            'courier' => 'Other',
            'city_id' => '',
            'area_id' => '',
            'weight' => 0.5,
        ],
        'products' => [],
    ]);

    $p1 = Product::factory()->create(['packaging_charge' => 45]);

    Livewire::test(EditOrder::class, ['order' => $order])
        ->assertSet('packaging_charge', 25)
        ->call('addProduct', $p1)
        ->assertSet('packaging_charge', 45);
});
