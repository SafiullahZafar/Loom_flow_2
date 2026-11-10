<?php

namespace Database\Factories;

use App\Models\FinancialAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FinancialAccount>
 */
class FinancialAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' '.fake()->randomElement(['Cash', 'Bank']),
            'type' => fake()->randomElement(['cash', 'bank']),
            'account_number' => fake()->optional()->numerify('##########'),
            'opening_balance' => fake()->randomFloat(2, 0, 1000000),
            'is_active' => true,
        ];
    }
}
