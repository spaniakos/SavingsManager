<?php

namespace Tests\Feature;

use App\Models\ExpenseEntry;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSuperCategory;
use App\Models\IncomeEntry;
use App\Models\IncomeCategory;
use App\Models\SavingsGoal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComprehensiveReportsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'IncomeCategorySeeder']);
        $this->artisan('db:seed', ['--class' => 'ExpenseSuperCategorySeeder']);
        $this->artisan('db:seed', ['--class' => 'ExpenseCategorySeeder']);
    }

    public function test_generate_comprehensive_report_with_breakdown(): void
    {
        $user = User::factory()->create([
            'seed_capital' => 1000.00,
        ]);

        $incomeCategory = IncomeCategory::first();
        $superCategory = ExpenseSuperCategory::where('name', 'essentials')->first();
        $expenseCategory = ExpenseCategory::where('expense_super_category_id', $superCategory->id)->first();

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Create income
        IncomeEntry::create([
            'user_id' => $user->id,
            'income_category_id' => $incomeCategory->id,
            'amount' => 3000.00,
            'date' => $startDate->copy()->addDays(5),
        ]);

        // Create expenses
        ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $expenseCategory->id,
            'amount' => 1500.00,
            'date' => $startDate->copy()->addDays(10),
        ]);

        // Create savings goal
        SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Test Goal',
            'target_amount' => 10000.00,
            'current_amount' => 2000.00,
            'initial_checkpoint' => 1000.00,
            'start_date' => Carbon::now()->subMonths(2),
            'target_date' => Carbon::now()->addMonths(6),
        ]);

        $this->actingAs($user);

        $response = $this->get('/admin/mobile/reports?' . http_build_query([
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'breakdown_type' => 'per_category',
            'generate' => '1',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('mobile.reports');
        $response->assertViewHas('reportData');
    }

    public function test_export_pdf_from_reports(): void
    {
        $user = User::factory()->create();

        $incomeCategory = IncomeCategory::first();
        $superCategory = ExpenseSuperCategory::where('name', 'essentials')->first();
        $expenseCategory = ExpenseCategory::where('expense_super_category_id', $superCategory->id)->first();

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        IncomeEntry::create([
            'user_id' => $user->id,
            'income_category_id' => $incomeCategory->id,
            'amount' => 2000.00,
            'date' => $startDate->copy()->addDays(5),
        ]);

        ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $expenseCategory->id,
            'amount' => 1000.00,
            'date' => $startDate->copy()->addDays(10),
        ]);

        $this->actingAs($user);

        // Then export to PDF
        $response = $this->get('/admin/mobile/reports/export-pdf?' . http_build_query([
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'breakdown_type' => 'per_category',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_report_shows_correct_hierarchical_breakdown(): void
    {
        $user = User::factory()->create();

        $incomeCategory = IncomeCategory::first();
        $superCategory = ExpenseSuperCategory::where('name', 'essentials')->first();
        $expenseCategory = ExpenseCategory::where('expense_super_category_id', $superCategory->id)->first();

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $expenseCategory->id,
            'amount' => 500.00,
            'date' => $startDate->copy()->addDays(10),
            'notes' => 'Test expense',
        ]);

        $this->actingAs($user);

        // Test per_super_category breakdown (level 0)
        $response = $this->get('/admin/mobile/reports?' . http_build_query([
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'breakdown_type' => 'per_super_category',
            'generate' => '1',
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('reportData');
        
        $reportData = $response->viewData('reportData');
        $this->assertArrayHasKey('expenses', $reportData);
    }
}

