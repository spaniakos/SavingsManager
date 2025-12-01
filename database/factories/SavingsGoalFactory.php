<?php

namespace Database\Factories;

use App\Models\SavingsGoal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SavingsGoal>
 */
class SavingsGoalFactory extends Factory
{
    protected $model = SavingsGoal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(3, true),
            'target_amount' => fake()->randomFloat(2, 1000, 50000),
            'current_amount' => fake()->randomFloat(2, 0, 10000),
            'initial_checkpoint' => 0,
            'start_date' => Carbon::now()->subMonths(1),
            'target_date' => Carbon::now()->addMonths(12),
        ];
    }
}
