<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'shop_name' => $this->faker->optional()->company(),
            'district' => $this->faker->optional()->city(),
            'email' => $this->faker->optional()->safeEmail(),
            'phone' => $this->faker->numerify('01#########'),
            'message' => $this->faker->sentence(12),
        ];
    }
}
