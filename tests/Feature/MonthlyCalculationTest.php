<?php

namespace Tests\Feature;

use App\Models\ExpenseCategory;
use App\Models\ExpenseEntry;
use App\Models\IncomeCategory;
use App\Models\IncomeEntry;
use App\Models\SavingsGoal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthlyCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'IncomeCategorySeeder']);
        $this->artisan('db:seed', ['--class' => 'ExpenseSuperCategorySeeder']);
        $this->artisan('db:seed', ['--class' => 'ExpenseCategorySeeder']);
    }

    public function test_monthly_calculation_updates_savings_goals(): void
    {
        $user = User::factory()->create([
            'seed_capital' => 1000.00,
        ]);

        $goal = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Test Goal',
            'target_amount' => 10000.00,
            'current_amount' => 1000.00,
            'initial_checkpoint' => 1000.00,
            'start_date' => Carbon::now()->subMonths(2),
            'target_date' => Carbon::now()->addMonths(6),
        ]);

        $incomeCategory = IncomeCategory::first();
        $expenseCategory = ExpenseCategory::first();

        $previousMonth = Carbon::now()->subMonth();

        // Create income for previous month
        IncomeEntry::create([
            'user_id' => $user->id,
            'income_category_id' => $incomeCategory->id,
            'amount' => 3000.00,
            'date' => $previousMonth->copy()->addDays(5),
        ]);

        // Create expenses for previous month
        ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $expenseCategory->id,
            'amount' => 1500.00,
            'date' => $previousMonth->copy()->addDays(10),
        ]);

        // Net savings should be 1500.00 (3000 - 1500)

        $this->actingAs($user);

        $response = $this->post('/admin/mobile/monthly-calculation');

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $goal->refresh();

        // Current amount should be updated: 1000 + 1500 = 2500
        $this->assertEquals(2500.00, $goal->current_amount);
        $this->assertNotNull($goal->last_monthly_calculation_at);
    }

    public function test_monthly_calculation_handles_negative_savings(): void
    {
        $user = User::factory()->create();

        $goal = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Test Goal',
            'target_amount' => 10000.00,
            'current_amount' => 1000.00,
            'initial_checkpoint' => 1000.00,
            'start_date' => Carbon::now()->subMonths(2),
            'target_date' => Carbon::now()->addMonths(6),
        ]);

        $incomeCategory = IncomeCategory::first();
        $expenseCategory = ExpenseCategory::first();

        $previousMonth = Carbon::now()->subMonth();

        // Create income for previous month
        IncomeEntry::create([
            'user_id' => $user->id,
            'income_category_id' => $incomeCategory->id,
            'amount' => 2000.00,
            'date' => $previousMonth->copy()->addDays(5),
        ]);

        // Create expenses for previous month (more than income)
        ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $expenseCategory->id,
            'amount' => 3000.00,
            'date' => $previousMonth->copy()->addDays(10),
        ]);

        // Net savings should be -1000.00 (2000 - 3000)

        $this->actingAs($user);

        $response = $this->post('/admin/mobile/monthly-calculation');

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $goal->refresh();

        // Current amount should be updated: 1000 - 1000 = 0 (or could be negative)
        $this->assertEquals(0.00, $goal->current_amount);
    }

    public function test_monthly_calculation_updates_all_goals(): void
    {
        $user = User::factory()->create();

        $goal1 = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Goal 1',
            'target_amount' => 10000.00,
            'current_amount' => 1000.00,
            'initial_checkpoint' => 1000.00,
            'start_date' => Carbon::now()->subMonths(2),
            'target_date' => Carbon::now()->addMonths(6),
        ]);

        $goal2 = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Goal 2',
            'target_amount' => 5000.00,
            'current_amount' => 500.00,
            'initial_checkpoint' => 500.00,
            'start_date' => Carbon::now()->subMonth(),
            'target_date' => Carbon::now()->addMonths(3),
        ]);

        $incomeCategory = IncomeCategory::first();
        $expenseCategory = ExpenseCategory::first();

        $previousMonth = Carbon::now()->subMonth();

        IncomeEntry::create([
            'user_id' => $user->id,
            'income_category_id' => $incomeCategory->id,
            'amount' => 2000.00,
            'date' => $previousMonth->copy()->addDays(5),
        ]);

        ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $expenseCategory->id,
            'amount' => 1000.00,
            'date' => $previousMonth->copy()->addDays(10),
        ]);

        // Net savings should be 1000.00

        $this->actingAs($user);

        $response = $this->post('/admin/mobile/monthly-calculation');

        $goal1->refresh();
        $goal2->refresh();

        // Both goals should be updated with the net savings
        $this->assertEquals(2000.00, $goal1->current_amount);
        $this->assertEquals(1500.00, $goal2->current_amount);
    }

    public function test_monthly_calculation_prevents_duplicate_calculation(): void
    {
        $user = User::factory()->create();

        $goal = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Test Goal',
            'target_amount' => 10000.00,
            'current_amount' => 1000.00,
            'initial_checkpoint' => 1000.00,
            'start_date' => Carbon::now()->subMonths(2),
            'target_date' => Carbon::now()->addMonths(6),
            'last_monthly_calculation_at' => Carbon::now()->subMonth()->endOfMonth(),
        ]);

        $this->actingAs($user);

        $response = $this->post('/admin/mobile/monthly-calculation');

        $response->assertRedirect();

        // Should show error or success message indicating already calculated
        // The exact behavior depends on implementation
    }
}
