<?php

namespace Database\Factories;

use App\Models\IncomeCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IncomeCategory>
 */
class IncomeCategoryFactory extends Factory
{
    protected $model = IncomeCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'is_system' => false,
            'user_id' => null,
        ];
    }
}
