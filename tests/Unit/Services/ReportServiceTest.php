<?php

namespace Tests\Unit\Services;

use App\Models\ExpenseEntry;
use App\Models\IncomeEntry;
use App\Models\SavingsGoal;
use App\Models\User;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ReportService $reportService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reportService = new ReportService();
        
        // Seed categories once for all tests
        $this->artisan('db:seed', ['--class' => 'IncomeCategorySeeder']);
        $this->artisan('db:seed', ['--class' => 'ExpenseSuperCategorySeeder']);
        $this->artisan('db:seed', ['--class' => 'ExpenseCategorySeeder']);
    }

    public function test_generate_monthly_report_calculates_totals(): void
    {
        $user = User::factory()->create();
        $month = Carbon::now()->startOfMonth();

        $incomeCategory = \App\Models\IncomeCategory::first();
        $expenseCategory = \App\Models\ExpenseCategory::first();

        // Create income entries
        IncomeEntry::create([
            'user_id' => $user->id,
            'income_category_id' => $incomeCategory->id,
            'amount' => 2000.00,
            'date' => $month->copy()->addDays(5),
        ]);

        IncomeEntry::create([
            'user_id' => $user->id,
            'income_category_id' => $incomeCategory->id,
            'amount' => 500.00,
            'date' => $month->copy()->addDays(15),
        ]);

        // Create expense entries
        ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $expenseCategory->id,
            'amount' => 800.00,
            'date' => $month->copy()->addDays(10),
        ]);

        ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $expenseCategory->id,
            'amount' => 200.00,
            'date' => $month->copy()->addDays(20),
        ]);

        $report = $this->reportService->generateMonthlyReport($user, $month);

        $this->assertEquals(2500.00, $report['summary']['total_income']);
        $this->assertEquals(1000.00, $report['summary']['total_expenses']);
        $this->assertEquals(1500.00, $report['summary']['net_savings']);
    }

    public function test_generate_monthly_report_includes_categories(): void
    {
        $user = User::factory()->create();
        $month = Carbon::now()->startOfMonth();

        // Create categories
        $incomeCategory = \App\Models\IncomeCategory::create([
            'name' => 'test_income_category',
            'is_system' => false,
            'user_id' => $user->id,
        ]);

        $superCategory = \App\Models\ExpenseSuperCategory::where('is_system', true)->first();
        $expenseCategory = \App\Models\ExpenseCategory::create([
            'name' => 'test_expense_category',
            'is_system' => false,
            'user_id' => $user->id,
            'expense_super_category_id' => $superCategory->id,
        ]);

        IncomeEntry::create([
            'user_id' => $user->id,
            'income_category_id' => $incomeCategory->id,
            'amount' => 1000.00,
            'date' => $month->copy()->addDays(5),
        ]);

        ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $expenseCategory->id,
            'amount' => 300.00,
            'date' => $month->copy()->addDays(10),
        ]);

        $report = $this->reportService->generateMonthlyReport($user, $month);

        $this->assertArrayHasKey('income_by_category', $report);
        $this->assertArrayHasKey('expenses_by_category', $report);
        $this->assertNotEmpty($report['income_by_category']);
        $this->assertNotEmpty($report['expenses_by_category']);
    }

    public function test_generate_category_expense_report_groups_by_super_category(): void
    {
        $user = User::factory()->create();
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Create super category and category
        $superCategory = \App\Models\ExpenseSuperCategory::where('is_system', true)->first();
        $category = \App\Models\ExpenseCategory::create([
            'name' => 'test_category',
            'is_system' => false,
            'user_id' => $user->id,
            'expense_super_category_id' => $superCategory->id,
        ]);

        ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
            'amount' => 100.00,
            'date' => $startDate->copy()->addDays(5),
        ]);

        ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
            'amount' => 100.00,
            'date' => $startDate->copy()->addDays(10),
        ]);

        ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
            'amount' => 100.00,
            'date' => $startDate->copy()->addDays(15),
        ]);

        $report = $this->reportService->generateCategoryExpenseReport($user, $startDate, $endDate);

        $this->assertArrayHasKey('expenses_by_super_category', $report);
        $this->assertEquals(300.00, $report['total_expenses']);
    }

    public function test_generate_savings_goal_report_includes_all_goals(): void
    {
        $user = User::factory()->create();

        SavingsGoal::factory()->create([
            'user_id' => $user->id,
            'target_amount' => 10000.00,
            'current_amount' => 3000.00,
        ]);

        SavingsGoal::factory()->create([
            'user_id' => $user->id,
            'target_amount' => 5000.00,
            'current_amount' => 2000.00,
        ]);

        $report = $this->reportService->generateSavingsGoalReport($user);

        $this->assertArrayHasKey('goals', $report);
        $this->assertCount(2, $report['goals']);
        $this->assertEquals(15000.00, $report['total_target']);
        $this->assertEquals(5000.00, $report['total_current']);
    }

    public function test_export_to_csv_generates_valid_csv(): void
    {
        $data = [
            ['name' => 'Test', 'amount' => 100.00],
            ['name' => 'Test2', 'amount' => 200.00],
        ];

        $csv = $this->reportService->exportToCsv($data);

        $this->assertIsString($csv);
        $this->assertStringContainsString('name', $csv);
        $this->assertStringContainsString('amount', $csv);
        $this->assertStringContainsString('Test', $csv);
    }
}
