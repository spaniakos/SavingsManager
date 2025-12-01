<?php

namespace Tests\Unit\Services;

use App\Models\SavingsGoal;
use App\Models\User;
use App\Services\SavingsCalculatorService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavingsCalculatorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SavingsCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SavingsCalculatorService();
    }

    public function test_calculate_monthly_saving_needed(): void
    {
        $user = User::factory()->create();
        
        $goal = SavingsGoal::factory()->create([
            'user_id' => $user->id,
            'target_amount' => 10000,
            'current_amount' => 2000,
            'target_date' => Carbon::now()->addMonths(8),
        ]);

        $monthlyNeeded = $this->service->calculateMonthlySavingNeeded($goal);
        
        $this->assertEquals(1000, $monthlyNeeded);
    }

    public function test_calculate_monthly_saving_needed_when_goal_reached(): void
    {
        $user = User::factory()->create();
        
        $goal = SavingsGoal::factory()->create([
            'user_id' => $user->id,
            'target_amount' => 10000,
            'current_amount' => 10000,
            'target_date' => Carbon::now()->addMonths(8),
        ]);

        $monthlyNeeded = $this->service->calculateMonthlySavingNeeded($goal);
        
        $this->assertEquals(0, $monthlyNeeded);
    }

    public function test_calculate_months_remaining(): void
    {
        $user = User::factory()->create();
        
        $goal = SavingsGoal::factory()->create([
            'user_id' => $user->id,
            'target_date' => Carbon::now()->addMonths(6),
        ]);

        $monthsRemaining = $this->service->calculateMonthsRemaining($goal);
        
        $this->assertGreaterThanOrEqual(5, $monthsRemaining);
        $this->assertLessThanOrEqual(7, $monthsRemaining);
    }

    public function test_calculate_months_remaining_when_past_due(): void
    {
        $user = User::factory()->create();
        
        $goal = SavingsGoal::factory()->create([
            'user_id' => $user->id,
            'target_date' => Carbon::now()->subMonths(1),
        ]);

        $monthsRemaining = $this->service->calculateMonthsRemaining($goal);
        
        $this->assertEquals(0, $monthsRemaining);
    }

    public function test_calculate_overall_progress(): void
    {
        $user = User::factory()->create();
        
        $goal = SavingsGoal::factory()->create([
            'user_id' => $user->id,
            'target_amount' => 10000,
            'current_amount' => 5000,
        ]);

        $progress = $this->service->calculateOverallProgress($goal);
        
        $this->assertEquals(50.0, $progress);
    }

    public function test_calculate_overall_progress_when_complete(): void
    {
        $user = User::factory()->create();
        
        $goal = SavingsGoal::factory()->create([
            'user_id' => $user->id,
            'target_amount' => 10000,
            'current_amount' => 10000,
        ]);

        $progress = $this->service->calculateOverallProgress($goal);
        
        $this->assertEquals(100.0, $progress);
    }

    public function test_calculate_overall_progress_when_over_target(): void
    {
        $user = User::factory()->create();
        
        $goal = SavingsGoal::factory()->create([
            'user_id' => $user->id,
            'target_amount' => 10000,
            'current_amount' => 12000,
        ]);

        $progress = $this->service->calculateOverallProgress($goal);
        
        $this->assertEquals(100.0, $progress); // Should cap at 100%
    }
}
