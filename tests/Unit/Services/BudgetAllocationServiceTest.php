<?php

namespace Tests\Unit\Services;

use App\Models\ExpenseCategory;
use App\Models\ExpenseEntry;
use App\Models\ExpenseSuperCategory;
use App\Models\User;
use App\Services\BudgetAllocationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetAllocationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BudgetAllocationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BudgetAllocationService();
        
        // Seed categories for tests
        $this->artisan('db:seed', ['--class' => 'ExpenseSuperCategorySeeder']);
        $this->artisan('db:seed', ['--class' => 'ExpenseCategorySeeder']);
    }

    public function test_calculate_super_category_allowance(): void
    {
        $user = User::factory()->create([
            'median_monthly_income' => 2000.00,
        ]);
        
        $superCategory = ExpenseSuperCategory::firstOrCreate(
            ['name' => 'essentials'],
            ['is_system' => true, 'allocation_percentage' => 50.00]
        );
        $superCategory->allocation_percentage = 50.00;
        $superCategory->save();

        $allowance = $this->service->calculateSuperCategoryAllowance($user, $superCategory);
        
        $this->assertEquals(1000.00, $allowance); // 50% of 2000
    }

    public function test_calculate_super_category_allowance_with_custom_income(): void
    {
        $user = User::factory()->create([
            'median_monthly_income' => 2000.00,
        ]);
        
        $superCategory = ExpenseSuperCategory::firstOrCreate(
            ['name' => 'lifestyle'],
            ['is_system' => true, 'allocation_percentage' => 30.00]
        );
        $superCategory->allocation_percentage = 30.00;
        $superCategory->save();

        $allowance = $this->service->calculateSuperCategoryAllowance($user, $superCategory, 3000.00);
        
        $this->assertEquals(900.00, $allowance); // 30% of 3000
    }

    public function test_get_spent_in_super_category(): void
    {
        $user = User::factory()->create();
        $superCategory = ExpenseSuperCategory::firstOrCreate(
            ['name' => 'essentials'],
            ['is_system' => true, 'allocation_percentage' => 50.00]
        );
        $category = ExpenseCategory::firstOrCreate(
            ['name' => 'test_category', 'expense_super_category_id' => $superCategory->id],
            ['is_system' => false]
        );
        
        ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
            'amount' => 500.00,
            'date' => Carbon::now()->startOfMonth(),
        ]);

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $spent = $this->service->getSpentInSuperCategory($user, $superCategory, $startDate, $endDate);
        
        $this->assertEquals(500.00, $spent);
    }

    public function test_get_remaining_allowance(): void
    {
        $user = User::factory()->create([
            'median_monthly_income' => 2000.00,
        ]);
        
        $superCategory = ExpenseSuperCategory::firstOrCreate(
            ['name' => 'essentials'],
            ['is_system' => true, 'allocation_percentage' => 50.00]
        );
        $superCategory->allocation_percentage = 50.00;
        $superCategory->save();
        
        $category = ExpenseCategory::firstOrCreate(
            ['name' => 'test_category_2', 'expense_super_category_id' => $superCategory->id],
            ['is_system' => false]
        );
        
        ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
            'amount' => 300.00,
            'date' => Carbon::now()->startOfMonth(),
        ]);

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $remaining = $this->service->getRemainingAllowance($user, $superCategory, $startDate, $endDate);
        
        $this->assertEquals(700.00, $remaining); // 1000 allowance - 300 spent
    }

    public function test_get_allocation_status(): void
    {
        $user = User::factory()->create([
            'median_monthly_income' => 2000.00,
        ]);

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $status = $this->service->getAllocationStatus($user, $startDate, $endDate);
        
        // Should return status for essentials, lifestyle, and savings
        $this->assertGreaterThanOrEqual(3, count($status)); // At least 3
        $this->assertArrayHasKey('super_category', $status[0]);
        $this->assertArrayHasKey('allocation_percentage', $status[0]);
        $this->assertArrayHasKey('allowance', $status[0]);
        $this->assertArrayHasKey('spent', $status[0]);
        $this->assertArrayHasKey('remaining', $status[0]);
        $this->assertArrayHasKey('spent_percentage', $status[0]);
        $this->assertArrayHasKey('is_over_budget', $status[0]);
        
        // Verify we have the 3 fixed categories
        $categoryNames = array_map(fn($s) => $s['super_category']->name, $status);
        $this->assertContains('essentials', $categoryNames);
        $this->assertContains('lifestyle', $categoryNames);
        $this->assertContains('savings', $categoryNames);
    }
}
