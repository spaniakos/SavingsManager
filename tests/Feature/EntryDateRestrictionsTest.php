<?php

namespace Tests\Feature;

use App\Models\ExpenseCategory;
use App\Models\ExpenseEntry;
use App\Models\ExpenseSuperCategory;
use App\Models\IncomeCategory;
use App\Models\SavingsGoal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntryDateRestrictionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'IncomeCategorySeeder']);
        $this->artisan('db:seed', ['--class' => 'ExpenseSuperCategorySeeder']);
        $this->artisan('db:seed', ['--class' => 'ExpenseCategorySeeder']);
    }

    public function test_cannot_create_expense_for_month_before_previous_month(): void
    {
        $user = User::factory()->create();

        $superCategory = ExpenseSuperCategory::where('name', 'essentials')->first();
        $category = ExpenseCategory::where('expense_super_category_id', $superCategory->id)->first();

        $this->actingAs($user);

        // Try to create expense for 2 months ago
        $oldDate = Carbon::now()->subMonths(2)->format('Y-m-d');

        $response = $this->post('/admin/mobile/expense/category/'.$category->id, [
            'amount' => 100.00,
            'date' => $oldDate,
            'notes' => 'Old expense',
        ]);

        $response->assertSessionHasErrors('date');
    }

    public function test_can_create_expense_for_current_month(): void
    {
        $user = User::factory()->create();

        $superCategory = ExpenseSuperCategory::where('name', 'essentials')->first();
        $category = ExpenseCategory::where('expense_super_category_id', $superCategory->id)->first();

        $this->actingAs($user);

        $currentDate = Carbon::now()->format('Y-m-d');

        $response = $this->post('/admin/mobile/expense/category/'.$category->id, [
            'amount' => 100.00,
            'date' => $currentDate,
            'notes' => 'Current expense',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('expense_entries', [
            'user_id' => $user->id,
            'amount' => 100.00,
        ]);
    }

    public function test_can_create_expense_for_previous_month_before_calculation(): void
    {
        $user = User::factory()->create();

        $superCategory = ExpenseSuperCategory::where('name', 'essentials')->first();
        $category = ExpenseCategory::where('expense_super_category_id', $superCategory->id)->first();

        $this->actingAs($user);

        $previousMonthDate = Carbon::now()->subMonth()->addDays(5)->format('Y-m-d');

        $response = $this->post('/admin/mobile/expense/category/'.$category->id, [
            'amount' => 100.00,
            'date' => $previousMonthDate,
            'notes' => 'Previous month expense',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('expense_entries', [
            'user_id' => $user->id,
            'amount' => 100.00,
        ]);
    }

    public function test_cannot_edit_expense_after_monthly_calculation(): void
    {
        $user = User::factory()->create();

        $superCategory = ExpenseSuperCategory::where('name', 'essentials')->first();
        $category = ExpenseCategory::where('expense_super_category_id', $superCategory->id)->first();

        $previousMonth = Carbon::now()->subMonth();

        $entry = ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
            'amount' => 100.00,
            'date' => $previousMonth->copy()->addDays(10),
        ]);

        // Create a goal and mark monthly calculation as done
        $goal = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Test Goal',
            'target_amount' => 10000.00,
            'current_amount' => 1000.00,
            'initial_checkpoint' => 1000.00,
            'start_date' => Carbon::now()->subMonths(2),
            'target_date' => Carbon::now()->addMonths(6),
            'last_monthly_calculation_at' => Carbon::now(),
        ]);

        $this->actingAs($user);

        $response = $this->put('/admin/mobile/expense-entries/'.$entry->id, [
            'amount' => 200.00,
            'date' => $entry->date->format('Y-m-d'),
            'notes' => 'Updated',
        ]);

        // Should redirect with error or prevent update
        $response->assertRedirect();

        // Entry should not be updated
        $entry->refresh();
        $this->assertEquals(100.00, $entry->amount);
    }

    public function test_cannot_delete_expense_after_monthly_calculation(): void
    {
        $user = User::factory()->create();

        $superCategory = ExpenseSuperCategory::where('name', 'essentials')->first();
        $category = ExpenseCategory::where('expense_super_category_id', $superCategory->id)->first();

        $previousMonth = Carbon::now()->subMonth();

        $entry = ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
            'amount' => 100.00,
            'date' => $previousMonth->copy()->addDays(10),
        ]);

        // Create a goal and mark monthly calculation as done
        $goal = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Test Goal',
            'target_amount' => 10000.00,
            'current_amount' => 1000.00,
            'initial_checkpoint' => 1000.00,
            'start_date' => Carbon::now()->subMonths(2),
            'target_date' => Carbon::now()->addMonths(6),
            'last_monthly_calculation_at' => Carbon::now(),
        ]);

        $this->actingAs($user);

        $response = $this->delete('/admin/mobile/expense-entries/'.$entry->id);

        $response->assertRedirect();

        // Entry should still exist
        $this->assertDatabaseHas('expense_entries', [
            'id' => $entry->id,
        ]);
    }

    public function test_cannot_create_income_for_month_before_previous_month(): void
    {
        $user = User::factory()->create();
        $category = IncomeCategory::first();

        $this->actingAs($user);

        // Try to create income for 2 months ago
        $oldDate = Carbon::now()->subMonths(2)->format('Y-m-d');

        $response = $this->post('/admin/mobile/income/category/'.$category->id, [
            'amount' => 1000.00,
            'date' => $oldDate,
            'notes' => 'Old income',
        ]);

        $response->assertSessionHasErrors('date');
    }
}
