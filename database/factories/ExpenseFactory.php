<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\FinancialAccount;
use App\Models\FixedExpense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fixed_expense_id' => null,
            'financial_account_id' => FinancialAccount::factory(),
            'expense_type' => 'variable',
            'title' => fake()->words(3, true),
            'expense_date' => fake()->date(),
            'amount' => fake()->randomFloat(2, 100, 100000),
            'payee' => fake()->optional()->company(),
            'reference' => fake()->optional()->bothify('EXP-####'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function fixed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'fixed_expense_id' => FixedExpense::factory(),
            'expense_type' => 'fixed',
        ]);
    }
}
