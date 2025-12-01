<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use App\Models\RecurringExpense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RecurringExpense>
 */
class RecurringExpenseFactory extends Factory
{
    protected $model = RecurringExpense::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $superCategory = \App\Models\ExpenseSuperCategory::where('name', 'essentials')->first();
        $category = $superCategory 
            ? \App\Models\ExpenseCategory::where('expense_super_category_id', $superCategory->id)->first()
            : null;

        return [
            'user_id' => User::factory(),
            'expense_category_id' => $category?->id ?? ExpenseCategory::factory(),
            'amount' => fake()->randomFloat(2, 10, 500),
            'frequency' => fake()->randomElement(['week', 'month', 'quarter', 'year']),
            'start_date' => Carbon::now()->subMonths(1),
            'end_date' => null,
            'last_generated_at' => null,
            'notes' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
