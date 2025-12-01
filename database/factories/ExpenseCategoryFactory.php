<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use App\Models\ExpenseSuperCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpenseCategory>
 */
class ExpenseCategoryFactory extends Factory
{
    protected $model = ExpenseCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $superCategory = ExpenseSuperCategory::where('name', 'essentials')->first();

        return [
            'name' => fake()->unique()->word(),
            'expense_super_category_id' => $superCategory?->id ?? ExpenseSuperCategory::factory(),
            'is_system' => false,
            'user_id' => null,
            'save_for_later_target' => null,
            'save_for_later_frequency' => null,
            'save_for_later_amount' => null,
        ];
    }
}
