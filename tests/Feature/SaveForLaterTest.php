<?php

namespace Tests\Feature;

use App\Models\ExpenseEntry;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSuperCategory;
use App\Models\SavingsGoal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaveForLaterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'ExpenseSuperCategorySeeder']);
        $this->artisan('db:seed', ['--class' => 'ExpenseCategorySeeder']);
    }

    public function test_save_for_later_expense_adds_to_savings_goals(): void
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

        $superCategory = ExpenseSuperCategory::where('name', 'essentials')->first();
        $category = ExpenseCategory::where('expense_super_category_id', $superCategory->id)->first();

        $this->actingAs($user);

        $response = $this->post('/admin/mobile/expense/category/' . $category->id, [
            'amount' => 500.00,
            'date' => Carbon::now()->format('Y-m-d'),
            'notes' => 'Save for later',
            'is_save_for_later' => true,
        ]);

        $response->assertRedirect();
        
        $goal->refresh();
        
        // Current amount should be increased by 500
        $this->assertEquals(1500.00, $goal->current_amount);
        
        // Expense entry should exist with is_save_for_later flag
        $this->assertDatabaseHas('expense_entries', [
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
            'amount' => 500.00,
            'is_save_for_later' => true,
        ]);
    }

    public function test_save_for_later_expense_adds_to_all_active_goals(): void
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

        $superCategory = ExpenseSuperCategory::where('name', 'essentials')->first();
        $category = ExpenseCategory::where('expense_super_category_id', $superCategory->id)->first();

        $this->actingAs($user);

        $response = $this->post('/admin/mobile/expense/category/' . $category->id, [
            'amount' => 300.00,
            'date' => Carbon::now()->format('Y-m-d'),
            'notes' => 'Save for later',
            'is_save_for_later' => true,
        ]);

        $response->assertRedirect();
        
        $goal1->refresh();
        $goal2->refresh();
        
        // Both goals should be increased by 300
        $this->assertEquals(1300.00, $goal1->current_amount);
        $this->assertEquals(800.00, $goal2->current_amount);
    }

    public function test_savings_category_expense_adds_to_goals(): void
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

        // Get savings super category
        $savingsSuperCategory = ExpenseSuperCategory::where('name', 'savings')->first();
        $savingsCategory = ExpenseCategory::where('expense_super_category_id', $savingsSuperCategory->id)->first();

        $this->actingAs($user);

        $response = $this->post('/admin/mobile/expense/category/' . $savingsCategory->id, [
            'amount' => 250.00,
            'date' => Carbon::now()->format('Y-m-d'),
            'notes' => 'Direct savings',
        ]);

        $response->assertRedirect();
        
        $goal->refresh();
        
        // Current amount should be increased by 250 (from savings category)
        $this->assertEquals(1250.00, $goal->current_amount);
    }
}

