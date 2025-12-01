<?php

namespace Database\Factories;

use App\Models\IncomeCategory;
use App\Models\IncomeEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IncomeEntry>
 */
class IncomeEntryFactory extends Factory
{
    protected $model = IncomeEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'income_category_id' => IncomeCategory::factory(),
            'amount' => $this->faker->randomFloat(2, 100, 5000),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
