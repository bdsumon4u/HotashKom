<?php

use App\Models\Admin;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = Admin::factory()->create();
    $this->product = Product::factory()->create();
    $this->variations = Product::factory()->count(3)->create([
        'parent_id' => $this->product->id,
    ]);
});

it('can bulk update product variations', function () {
    $variationData = [
        'variations' => [
            [
                'id' => $this->variations[0]->id,
                'price' => 100.00,
                'selling_price' => 120.00,
                'suggested_price' => 130.00,
                'sku' => 'SKU-001',
                'should_track' => true,
                'stock_count' => 50,
            ],
            [
                'id' => $this->variations[1]->id,
                'price' => 200.00,
                'selling_price' => 240.00,
                'suggested_price' => 260.00,
                'sku' => 'SKU-002',
                'should_track' => false,
                'stock_count' => 0,
            ],
        ],
    ];

    $response = $this->actingAs($this->admin, 'admin')
        ->patch(route('admin.products.variations.bulk-update', $this->product), $variationData);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'All variations have been updated successfully.');

    // Verify first variation was updated
    $this->variations[0]->refresh();
    expect($this->variations[0]->price)->toEqual(100);
    expect($this->variations[0]->selling_price)->toEqual(120);
    expect($this->variations[0]->suggested_price)->toEqual(130);
    expect($this->variations[0]->sku)->toBe('SKU-001');
    expect($this->variations[0]->should_track)->toBeTrue();
    expect($this->variations[0]->stock_count)->toBe(50);

    // Verify second variation was updated
    $this->variations[1]->refresh();
    expect($this->variations[1]->price)->toEqual(200);
    expect($this->variations[1]->selling_price)->toEqual(240);
    expect($this->variations[1]->suggested_price)->toEqual(260);
    expect($this->variations[1]->sku)->toBe('SKU-002');
    expect($this->variations[1]->should_track)->toBeFalse();
    expect($this->variations[1]->stock_count)->toBe(0);
});

it('validates required fields for bulk update', function () {
    $variationData = [
        'variations' => [
            [
                'id' => $this->variations[0]->id,
                'price' => '', // Missing required field
                'selling_price' => 120.00,
                'sku' => 'SKU-001',
                'should_track' => true,
                'stock_count' => 50,
            ],
        ],
    ];

    $response = $this->actingAs($this->admin, 'admin')
        ->patch(route('admin.products.variations.bulk-update', $this->product), $variationData);

    $response->assertSessionHasErrors('variations.0.price');
});

it('prevents salesman from bulk updating variations', function () {
    $salesman = Admin::factory()->salesman()->create();

    $variationData = [
        'variations' => [
            [
                'id' => $this->variations[0]->id,
                'price' => 100.00,
                'selling_price' => 120.00,
                'sku' => 'SKU-001',
                'should_track' => true,
                'stock_count' => 50,
            ],
        ],
    ];

    $response = $this->actingAs($salesman, 'admin')
        ->patch(route('admin.products.variations.bulk-update', $this->product), $variationData);

    $response->assertStatus(403);
});

it('handles empty variations array', function () {
    $variationData = ['variations' => []];

    $response = $this->actingAs($this->admin, 'admin')
        ->patch(route('admin.products.variations.bulk-update', $this->product), $variationData);

    $response->assertRedirect();
    $response->assertSessionHas('danger', 'No variations to update.');
});
