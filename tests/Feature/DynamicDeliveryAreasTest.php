<?php

use App\Models\Admin;
use App\Models\Order;
use App\Models\Setting;

beforeEach(function () {
    // Clear cache before each test
    cacheMemo()->forget('settings');
    cacheMemo()->flush();
});

it('automatically provides default fallback delivery areas', function () {
    // Force delete existing delivery settings to test fallback
    Setting::whereIn('name', ['delivery_areas', 'delivery_charge', 'default_area'])->delete();

    $settings = Setting::array();

    expect($settings)->toHaveKey('delivery_areas');
    $areas = $settings['delivery_areas'];
    expect($areas)->toHaveCount(2);
    expect($areas[0]['name'])->toBe('Inside Dhaka');
    expect($areas[0]['is_default'])->toBeTrue();
    expect($areas[1]['name'])->toBe('Outside Dhaka');
});

it('allows admin to save custom delivery areas and reconstructs compatibility settings', function () {
    $admin = Admin::factory()->create();

    $payload = [
        'tab' => 'delivery',
        'delivery_areas' => [
            ['name' => 'Dhaka City', 'cost' => 60],
            ['name' => 'Suburbs', 'cost' => 100],
            ['name' => 'Chittagong', 'cost' => 150],
        ],
        'default_delivery_area' => 1, // Suburbs is default
        'delivery_text' => 'Fast delivery',
        'free_delivery' => ['enabled' => 0],
        'show_option' => ['productwise_delivery_charge' => 0, 'quantitywise_delivery_charge' => 0],
    ];

    $response = $this
        ->actingAs($admin, 'admin')
        ->patch(route('admin.settings'), $payload);

    $response->assertRedirect();

    // Verify settings stored in DB
    $settings = Setting::array();

    expect($settings['delivery_areas'])->toHaveCount(3);
    expect(data_get($settings['delivery_areas'][0], 'name'))->toBe('Dhaka City');
    expect((bool) data_get($settings['delivery_areas'][0], 'is_default'))->toBeFalse();
    expect(data_get($settings['delivery_areas'][1], 'name'))->toBe('Suburbs');
    expect((bool) data_get($settings['delivery_areas'][1], 'is_default'))->toBeTrue();

    // Verify compatibility delivery_charge/default_area
    expect($settings['delivery_charge']->inside_dhaka)->toBe(60);
    expect($settings['delivery_charge']->outside_dhaka)->toBe(100);
    expect($settings['default_area']->inside)->toBeFalse();
    expect($settings['default_area']->outside)->toBeTrue();
});

it('calculates shipping cost based on dynamic delivery areas', function () {
    Setting::updateOrCreate(['name' => 'delivery_areas'], [
        'value' => [
            ['name' => 'Sylhet', 'cost' => 140, 'is_default' => false],
            ['name' => 'Dhaka City', 'cost' => 50, 'is_default' => true],
        ],
    ]);
    Setting::updateOrCreate(['name' => 'show_option'], [
        'value' => ['productwise_delivery_charge' => 0],
    ]);

    cacheMemo()->forget('settings');

    $order = new Order;
    $products = collect([
        ['id' => 1, 'quantity' => 2, 'price' => 100],
    ]);

    // Test with Sylhet
    $costSylhet = $order->getShippingCost($products, 200, 'Sylhet');
    expect($costSylhet)->toBe(140);

    // Test with Dhaka City
    $costDhaka = $order->getShippingCost($products, 200, 'Dhaka City');
    expect($costDhaka)->toBe(50);
});
