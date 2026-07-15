<?php

use App\Models\Admin;
use App\Models\Image;
use App\Models\Product;
use App\Models\Setting;

beforeEach(function () {
    // Clear cache before each test
    cacheMemo()->forget('settings');
    cacheMemo()->flush();
});

it('allows admin to save company SEO and homepage settings', function () {
    $this->withoutExceptionHandling();
    $admin = Admin::factory()->create();

    $payload = [
        'tab' => 'company',
        'company' => [
            'name' => 'Test Shop',
            'contact_name' => 'John Doe',
            'email' => 'admin@test.com',
            'phone' => '01700000000',
            'whatsapp' => '01700000000',
            'tagline' => 'Best Shop',
            'address' => 'Dhaka',
            'office_time' => '9 AM - 6 PM',
            'home_heading' => 'Welcome to Test Shop',
            'seo_title' => 'Custom Shop SEO Title',
            'meta_description' => 'Custom Shop Meta Description',
        ],
        'call_for_order' => '01700000000',
        'social' => [
            'facebook' => ['link' => 'https://facebook.com'],
        ],
    ];

    $response = $this
        ->actingAs($admin, 'admin')
        ->patch(route('admin.settings'), $payload);

    $response->assertRedirect();

    $settings = Setting::array();

    expect($settings['company']->home_heading)->toBe('Welcome to Test Shop');
    expect($settings['company']->seo_title)->toBe('Custom Shop SEO Title');
    expect($settings['company']->meta_description)->toBe('Custom Shop Meta Description');
});

it('renders SEO title, meta description, and H1 heading on the homepage', function () {
    $this->withoutExceptionHandling();
    Setting::updateOrCreate(['name' => 'company'], [
        'value' => [
            'name' => 'Test Shop',
            'contact_name' => 'John Doe',
            'email' => 'admin@test.com',
            'phone' => '01700000000',
            'whatsapp' => '01700000000',
            'tagline' => 'Best Shop',
            'address' => 'Dhaka',
            'office_time' => '9 AM - 6 PM',
            'home_heading' => 'Welcome to Test Shop',
            'seo_title' => 'Custom Shop SEO Title',
            'meta_description' => 'Custom Shop Meta Description',
        ],
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('<title>Custom Shop SEO Title</title>', false);
    $response->assertSee('<meta name="description" content="Custom Shop Meta Description">', false);
    $response->assertSee('Welcome to Test Shop');
});

it('renders fallback product SEO on product detail page', function () {
    $this->withoutExceptionHandling();

    $image = Image::create([
        'filename' => 'product.jpg',
        'disk' => 'public',
        'path' => 'uploads/product.jpg',
        'extension' => 'jpg',
        'mime' => 'image/jpeg',
        'size' => 1024,
    ]);

    $product = Product::factory()->create([
        'name' => 'Aesthetic Product',
        'description' => 'Beautiful aesthetics with dynamic SEO fallback.',
        'short_description' => 'Beautiful aesthetics.',
    ]);

    $product->images()->attach($image->id, ['img_type' => 'base', 'order' => 1]);

    $response = $this->get(route('products.show', $product));

    $response->assertStatus(200);
    $response->assertSee('Aesthetic Product');
});
