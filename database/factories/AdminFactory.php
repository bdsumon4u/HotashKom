<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'role_id' => Admin::ADMIN,
            'is_active' => true,
        ];
    }

    public function salesman(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Admin::SALESMAN,
        ]);
    }
}
