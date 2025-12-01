<?php

namespace Tests\Unit\Services;

use App\Models\ExpenseCategory;
use App\Models\ExpenseEntry;
use App\Models\ExpenseSuperCategory;
use App\Models\RecurringExpense;
use App\Models\User;
use App\Services\RecurringExpenseService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecurringExpenseServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RecurringExpenseService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RecurringExpenseService();
        
        // Seed categories for tests
        $this->artisan('db:seed', ['--class' => 'ExpenseSuperCategorySeeder']);
        $this->artisan('db:seed', ['--class' => 'ExpenseCategorySeeder']);
    }

    public function test_calculate_next_due_date_monthly(): void
    {
        $user = User::factory()->create();
        
        // Create super category and category if they don't exist
        $superCategory = ExpenseSuperCategory::firstOrCreate(
            ['name' => 'essentials'],
            ['is_system' => true, 'allocation_percentage' => 50.00]
        );
        
        $category = ExpenseCategory::firstOrCreate(
            ['name' => 'test_category', 'expense_super_category_id' => $superCategory->id],
            ['is_system' => false]
        );
        
        $recurring = RecurringExpense::create([
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
            'amount' => 100.00,
            'frequency' => 'month',
            'start_date' => Carbon::now()->subMonths(2),
            'is_active' => true,
        ]);

        $nextDue = $this->service->calculateNextDueDate($recurring);
        
        $this->assertNotNull($nextDue);
        $this->assertTrue($nextDue->isFuture());
    }

    public function test_calculate_next_due_date_weekly(): void
    {
        $user = User::factory()->create();
        $superCategory = ExpenseSuperCategory::firstOrCreate(
            ['name' => 'essentials'],
            ['is_system' => true, 'allocation_percentage' => 50.00]
        );
        $category = ExpenseCategory::firstOrCreate(
            ['name' => 'test_category_2', 'expense_super_category_id' => $superCategory->id],
            ['is_system' => false]
        );
        
        $recurring = RecurringExpense::create([
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
            'amount' => 50.00,
            'frequency' => 'week',
            'start_date' => Carbon::now()->subWeeks(2),
            'is_active' => true,
        ]);

        $nextDue = $this->service->calculateNextDueDate($recurring);
        
        $this->assertNotNull($nextDue);
        $this->assertTrue($nextDue->isFuture());
    }

    public function test_calculate_next_due_date_with_end_date(): void
    {
        $user = User::factory()->create();
        $superCategory = ExpenseSuperCategory::firstOrCreate(
            ['name' => 'essentials'],
            ['is_system' => true, 'allocation_percentage' => 50.00]
        );
        $category = ExpenseCategory::firstOrCreate(
            ['name' => 'test_category_3', 'expense_super_category_id' => $superCategory->id],
            ['is_system' => false]
        );
        
        $recurring = RecurringExpense::create([
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
            'amount' => 100.00,
            'frequency' => 'month',
            'start_date' => Carbon::now()->subMonths(6),
            'end_date' => Carbon::now()->subMonths(1),
            'is_active' => true,
        ]);

        $nextDue = $this->service->calculateNextDueDate($recurring);
        
        $this->assertNull($nextDue); // Should be null because end_date has passed
    }

    public function test_get_upcoming_recurring_expenses(): void
    {
        $user = User::factory()->create();
        $superCategory = ExpenseSuperCategory::firstOrCreate(
            ['name' => 'essentials'],
            ['is_system' => true, 'allocation_percentage' => 50.00]
        );
        $category = ExpenseCategory::firstOrCreate(
            ['name' => 'test_category_4', 'expense_super_category_id' => $superCategory->id],
            ['is_system' => false]
        );
        
        // Create recurring expense that will be due soon
        RecurringExpense::create([
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
            'amount' => 100.00,
            'frequency' => 'week',
            'start_date' => Carbon::now()->subDays(5), // Started 5 days ago, next due soon
            'is_active' => true,
        ]);

        $upcoming = $this->service->getUpcomingRecurringExpenses($user->id, 30);
        
        $this->assertNotEmpty($upcoming);
        $this->assertArrayHasKey('recurring', $upcoming[0]);
        $this->assertArrayHasKey('next_due_date', $upcoming[0]);
        $this->assertArrayHasKey('amount', $upcoming[0]);
    }
}
