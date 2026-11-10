<?php

namespace Database\Factories;

use App\Models\FixedExpense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FixedExpense>
 */
class FixedExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'payee' => fake()->optional()->company(),
            'default_amount' => fake()->randomFloat(2, 100, 100000),
            'frequency' => fake()->randomElement(['monthly', 'quarterly', 'yearly']),
            'due_day' => fake()->numberBetween(1, 28),
            'is_active' => true,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
