<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand_id' => Brand::factory(),
            'name' => fake()->words(3, true),
            'slug' => fake()->slug(),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'average_purchase_price' => fake()->randomFloat(2, 5, 500),
            'selling_price' => fake()->randomFloat(2, 15, 1200),
            'suggested_price' => fake()->randomFloat(2, 20, 1500),
            'sku' => fake()->unique()->bothify('SKU-####'),
            'should_track' => fake()->boolean(),
            'stock_count' => fake()->numberBetween(0, 100),
            'desc_img_pos' => fake()->randomElement(['top', 'bottom', 'left', 'right']),
            'is_active' => true,
            'hot_sale' => fake()->boolean(),
            'new_arrival' => fake()->boolean(),
            'shipping_inside' => fake()->numberBetween(50, 200),
            'shipping_outside' => fake()->numberBetween(100, 500),
            'delivery_text' => fake()->sentence(),
        ];
    }
}
