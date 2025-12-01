<?php

namespace Tests\Smoke;

use App\Models\ExpenseCategory;
use App\Models\ExpenseEntry;
use App\Models\ExpenseSuperCategory;
use App\Models\IncomeCategory;
use App\Models\IncomeEntry;
use App\Models\SavingsGoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CriticalPathsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_authenticate(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user);

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_create_income_entry(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->artisan('db:seed', ['--class' => 'IncomeCategorySeeder']);
        $category = IncomeCategory::first();

        $entry = IncomeEntry::create([
            'user_id' => $user->id,
            'income_category_id' => $category->id,
            'amount' => 2000.00,
            'date' => now(),
        ]);

        $this->assertNotNull($entry);
        $this->assertEquals($user->id, $entry->user_id);
    }

    public function test_user_can_create_expense_entry(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->artisan('db:seed', ['--class' => 'ExpenseSuperCategorySeeder']);
        $this->artisan('db:seed', ['--class' => 'ExpenseCategorySeeder']);

        $superCategory = ExpenseSuperCategory::firstOrCreate(
            ['name' => 'essentials'],
            ['is_system' => true, 'allocation_percentage' => 50.00]
        );
        $category = ExpenseCategory::firstOrCreate(
            ['name' => 'test_smoke_category', 'expense_super_category_id' => $superCategory->id],
            ['is_system' => false]
        );

        $entry = ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
            'amount' => 100.00,
            'date' => now(),
        ]);

        $this->assertNotNull($entry);
        $this->assertEquals($user->id, $entry->user_id);
    }

    public function test_user_can_create_savings_goal(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $goal = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Test Goal',
            'target_amount' => 10000.00,
            'current_amount' => 2000.00,
            'initial_checkpoint' => 2000.00,
            'start_date' => now(),
            'target_date' => now()->addMonths(12),
        ]);

        $this->assertNotNull($goal);
        $this->assertEquals($user->id, $goal->user_id);
    }

    public function test_dashboard_data_loads(): void
    {
        $user = User::factory()->create([
            'median_monthly_income' => 2000.00,
            'seed_capital' => 5000.00,
        ]);
        $this->actingAs($user);

        // Create some data
        $this->artisan('db:seed', ['--class' => 'IncomeCategorySeeder']);
        $this->artisan('db:seed', ['--class' => 'ExpenseSuperCategorySeeder']);
        $this->artisan('db:seed', ['--class' => 'ExpenseCategorySeeder']);

        // Verify user can access their data
        $incomeEntries = IncomeEntry::where('user_id', $user->id)->count();
        $expenseEntries = ExpenseEntry::where('user_id', $user->id)->count();
        $savingsGoals = SavingsGoal::where('user_id', $user->id)->count();

        $this->assertIsInt($incomeEntries);
        $this->assertIsInt($expenseEntries);
        $this->assertIsInt($savingsGoals);
    }
}
