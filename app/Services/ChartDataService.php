<?php

namespace App\Services;

use App\Models\ExpenseEntry;
use App\Models\IncomeEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChartDataService
{
    /**
     * Get expenses aggregated by category for a given period
     */
    public function getExpensesByCategory(?Carbon $startDate = null, ?Carbon $endDate = null, ?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();
        $startDate = $startDate ?? Carbon::now()->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        return ExpenseEntry::where('expense_entries.user_id', $userId)
            ->whereBetween('expense_entries.date', [$startDate, $endDate])
            ->join('expense_categories', 'expense_entries.expense_category_id', '=', 'expense_categories.id')
            ->select(
                'expense_categories.name as category_name',
                DB::raw('SUM(expense_entries.amount) as total')
            )
            ->groupBy('expense_categories.name')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => __("categories.expense.{$item->category_name}"),
                    'value' => (float) $item->total,
                ];
            })
            ->toArray();
    }

    /**
     * Get expenses aggregated by super category for a given period
     */
    public function getExpensesBySuperCategory(?Carbon $startDate = null, ?Carbon $endDate = null, ?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();
        $startDate = $startDate ?? Carbon::now()->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        return ExpenseEntry::where('expense_entries.user_id', $userId)
            ->whereBetween('expense_entries.date', [$startDate, $endDate])
            ->join('expense_categories', 'expense_entries.expense_category_id', '=', 'expense_categories.id')
            ->join('expense_super_categories', 'expense_categories.expense_super_category_id', '=', 'expense_super_categories.id')
            ->select(
                'expense_super_categories.name as super_category_name',
                DB::raw('SUM(expense_entries.amount) as total')
            )
            ->groupBy('expense_super_categories.name')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => __("categories.expense_super.{$item->super_category_name}"),
                    'value' => (float) $item->total,
                ];
            })
            ->toArray();
    }

    /**
     * Get expenses per item within a category
     */
    public function getExpensesByItemInCategory(string $categoryName, ?Carbon $startDate = null, ?Carbon $endDate = null, ?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();
        $startDate = $startDate ?? Carbon::now()->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        return ExpenseEntry::where('expense_entries.user_id', $userId)
            ->whereBetween('expense_entries.date', [$startDate, $endDate])
            ->join('expense_categories', 'expense_entries.expense_category_id', '=', 'expense_categories.id')
            ->where('expense_categories.name', $categoryName)
            ->select(
                'expense_entries.notes as item_name',
                DB::raw('SUM(expense_entries.amount) as total')
            )
            ->groupBy('expense_entries.notes')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->item_name ?: __('common.uncategorized'),
                    'value' => (float) $item->total,
                ];
            })
            ->toArray();
    }

    /**
     * Get income trends over time
     */
    public function getIncomeTrends(?Carbon $startDate = null, ?Carbon $endDate = null, ?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();
        $startDate = $startDate ?? Carbon::now()->subMonths(6)->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        return IncomeEntry::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => Carbon::createFromFormat('Y-m', $item->month)->format('M Y'),
                    'value' => (float) $item->total,
                ];
            })
            ->toArray();
    }

    /**
     * Get income by category
     */
    public function getIncomeByCategory(?Carbon $startDate = null, ?Carbon $endDate = null, ?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();
        $startDate = $startDate ?? Carbon::now()->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        return IncomeEntry::where('income_entries.user_id', $userId)
            ->whereBetween('income_entries.date', [$startDate, $endDate])
            ->join('income_categories', 'income_entries.income_category_id', '=', 'income_categories.id')
            ->select(
                'income_categories.name as category_name',
                DB::raw('SUM(income_entries.amount) as total')
            )
            ->groupBy('income_categories.name')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => __("categories.income.{$item->category_name}"),
                    'value' => (float) $item->total,
                ];
            })
            ->toArray();
    }

    /**
     * Get month-over-month savings comparison
     */
    public function getMoMSavingsComparison(int $months = 6, ?int $userId = null): array
    {
        $userId = $userId ?? Auth::id();
        $startDate = Carbon::now()->subMonths($months)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $data = [];
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            $income = IncomeEntry::where('user_id', $userId)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount');

            $expenses = ExpenseEntry::where('user_id', $userId)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount');

            $savings = $income - $expenses;

            $data[] = [
                'label' => $current->format('M Y'),
                'income' => (float) $income,
                'expenses' => (float) $expenses,
                'savings' => (float) $savings,
            ];

            $current->addMonth();
        }

        return $data;
    }

    /**
     * Format data for Chart.js pie/doughnut chart
     */
    public function formatForPieChart(array $data): array
    {
        return [
            'labels' => array_column($data, 'label'),
            'datasets' => [
                [
                    'data' => array_column($data, 'value'),
                ],
            ],
        ];
    }

    /**
     * Format data for Chart.js bar/line chart
     */
    public function formatForBarChart(array $data): array
    {
        return [
            'labels' => array_column($data, 'label'),
            'datasets' => [
                [
                    'label' => __('common.amount'),
                    'data' => array_column($data, 'value'),
                ],
            ],
        ];
    }

    /**
     * Format data for MoM comparison chart
     */
    public function formatForMoMChart(array $data): array
    {
        return [
            'labels' => array_column($data, 'label'),
            'datasets' => [
                [
                    'label' => __('common.income'),
                    'data' => array_column($data, 'income'),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                ],
                [
                    'label' => __('common.expenses'),
                    'data' => array_column($data, 'expenses'),
                    'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                ],
                [
                    'label' => __('common.savings'),
                    'data' => array_column($data, 'savings'),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                ],
            ],
        ];
    }
}
