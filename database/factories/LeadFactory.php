<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
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
