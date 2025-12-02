<?php

namespace App\Services;

use App\Models\ExpenseEntry;
use App\Models\IncomeEntry;
use App\Models\Person;
use App\Models\SavingsGoal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Calculate total saved money (seed_capital + sum(initial_checkpoint + current_amount for all goals))
     * Note: save_for_later expenses are NOT added separately because they already modify current_amount
     */
    public function calculateTotalSaved(User $user): float
    {
        $seedCapital = $user->seed_capital ?? 0;

        // Sum of all savings goals (initial_checkpoint + current_amount)
        // Note: current_amount already includes contributions from Savings category expenses and save_for_later expenses
        $savingsGoalsTotal = SavingsGoal::where('user_id', $user->id)
            ->get()
            ->sum(function ($goal) {
                return ($goal->initial_checkpoint ?? 0) + ($goal->current_amount ?? 0);
            });

        return round($seedCapital + $savingsGoalsTotal, 2);
    }

    /**
     * Generate monthly report for a user
     */
    public function generateMonthlyReport(User $user, Carbon $month): array
    {
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        $income = IncomeEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $expenses = ExpenseEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $savings = $income - $expenses;
        $savingsRate = $income > 0 ? ($savings / $income) * 100 : 0;
        $totalSaved = $this->calculateTotalSaved($user);

        $incomeByCategory = IncomeEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->select('income_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('income_category_id')
            ->with('incomeCategory')
            ->get()
            ->mapWithKeys(function ($entry) {
                return [$entry->incomeCategory->getTranslatedName() => $entry->total];
            });

        $expensesByCategory = ExpenseEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->select('expense_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('expense_category_id')
            ->with('expenseCategory')
            ->get()
            ->mapWithKeys(function ($entry) {
                return [$entry->expenseCategory->getTranslatedName() => $entry->total];
            });

        return [
            'month' => $month->format('F Y'),
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'total_income' => $income,
                'total_expenses' => $expenses,
                'net_savings' => $savings,
                'savings_rate' => round($savingsRate, 2),
                'total_saved' => $totalSaved,
            ],
            'income_by_category' => $incomeByCategory,
            'expenses_by_category' => $expensesByCategory,
            'savings_goals_progress' => $this->getSavingsGoalsProgress($user, $month),
        ];
    }

    /**
     * Generate category-wise expense report
     */
    public function generateCategoryExpenseReport(User $user, Carbon $startDate, Carbon $endDate, ?int $categoryId = null): array
    {
        $query = ExpenseEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($categoryId) {
            $query->where('expense_category_id', $categoryId);
        }

        $expenses = $query->with('expenseCategory.expenseSuperCategory')
            ->get()
            ->groupBy(function ($entry) {
                return $entry->expenseCategory->expenseSuperCategory->getTranslatedName();
            })
            ->map(function ($group) {
                return [
                    'total' => $group->sum('amount'),
                    'count' => $group->count(),
                    'categories' => $group->groupBy('expense_category_id')
                        ->map(function ($categoryGroup) {
                            $category = $categoryGroup->first()->expenseCategory;

                            return [
                                'name' => $category->getTranslatedName(),
                                'total' => $categoryGroup->sum('amount'),
                                'count' => $categoryGroup->count(),
                            ];
                        })->values(),
                ];
            });

        $totalSaved = $this->calculateTotalSaved($user);

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'expenses_by_super_category' => $expenses,
            'total_expenses' => $expenses->sum('total'),
            'total_saved' => $totalSaved,
        ];
    }

    /**
     * Generate savings goal progress report
     */
    public function generateSavingsGoalReport(User $user, ?Carbon $month = null): array
    {
        $month = $month ?? Carbon::now();

        $goals = SavingsGoal::where('user_id', $user->id)
            ->get();

        $calculator = app(SavingsCalculatorService::class);

        $totalSaved = $this->calculateTotalSaved($user);

        return [
            'month' => $month->format('F Y'),
            'goals' => $goals->map(function ($goal) use ($calculator) {
                return [
                    'id' => $goal->id,
                    'name' => $goal->name,
                    'target_amount' => $goal->target_amount,
                    'current_amount' => $goal->current_amount,
                    'progress_percentage' => $calculator->calculateOverallProgress($goal),
                    'monthly_saving_needed' => $calculator->calculateMonthlySavingNeeded($goal),
                    'months_remaining' => $calculator->calculateMonthsRemaining($goal),
                ];
            }),
            'total_target' => $goals->sum('target_amount'),
            'total_current' => $goals->sum('current_amount'),
            'total_progress' => $goals->sum('target_amount') > 0
                ? ($goals->sum('current_amount') / $goals->sum('target_amount')) * 100
                : 0,
            'total_saved' => $totalSaved,
        ];
    }

    /**
     * Get savings goals progress for a month
     */
    protected function getSavingsGoalsProgress(User $user, Carbon $month): array
    {
        $goals = SavingsGoal::where('user_id', $user->id)
            ->get();

        $calculator = app(SavingsCalculatorService::class);

        return $goals->map(function ($goal) use ($calculator) {
            return [
                'name' => $goal->name,
                'progress' => $calculator->calculateOverallProgress($goal),
            ];
        })->toArray();
    }

    /**
     * Get savings goals progress for comprehensive report
     */
    protected function getSavingsGoalsProgressForReport(User $user): array
    {
        $goals = SavingsGoal::where('user_id', $user->id)
            ->get();

        $calculator = app(SavingsCalculatorService::class);

        return $goals->map(function ($goal) use ($calculator) {
            return [
                'name' => $goal->name,
                'target_amount' => $goal->target_amount,
                'current_amount' => $goal->current_amount ?? 0,
                'progress' => $calculator->calculateOverallProgress($goal),
            ];
        })->toArray();
    }

    /**
     * Generate comprehensive report with all breakdowns
     */
    public function generateComprehensiveReport(User $user, Carbon $startDate, Carbon $endDate, string $breakdownType = 'super_category', ?int $personId = null): array
    {
        // Income and Expenses Summary
        $incomeQuery = IncomeEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate]);
        
        // Filter income by person if specified
        if ($personId !== null) {
            $incomeQuery->where('person_id', $personId);
        }
        $totalIncome = $incomeQuery->sum('amount');

        $expenseQuery = ExpenseEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate]);
        
        // Filter expenses by person if specified
        if ($personId !== null) {
            $expenseQuery->where('person_id', $personId);
        }
        $totalExpenses = $expenseQuery->sum('amount');

        $netSavings = $totalIncome - $totalExpenses;
        $savingsRate = $totalIncome > 0 ? ($netSavings / $totalIncome) * 100 : 0;
        $totalSaved = $this->calculateTotalSaved($user);

        // Income breakdowns - hierarchical structure
        $incomeHierarchical = $this->getIncomeHierarchical($user, $startDate, $endDate, $breakdownType, $personId);

        // Expense breakdowns - hierarchical structure
        $expensesHierarchical = $this->getExpensesHierarchical($user, $startDate, $endDate, $breakdownType, $personId);

        // Goal progression
        $goalsProgress = $this->getSavingsGoalsProgressForReport($user);

        // Calculate personal expense totals by person
        $personalExpenseTotals = $this->calculatePersonalExpenseTotals($user, $startDate, $endDate);

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'start_formatted' => $startDate->format('d/m/Y'),
                'end_formatted' => $endDate->format('d/m/Y'),
            ],
            'summary' => [
                'total_income' => round($totalIncome, 2),
                'total_expenses' => round($totalExpenses, 2),
                'net_savings' => round($netSavings, 2),
                'savings_rate' => round($savingsRate, 2),
                'total_saved' => round($totalSaved, 2),
            ],
            'income' => [
                'hierarchical' => $incomeHierarchical,
            ],
            'expenses' => [
                'hierarchical' => $expensesHierarchical,
            ],
            'goals_progress' => $goalsProgress,
            'breakdown_type' => $breakdownType,
            'person_id' => $personId,
            'personal_expense_totals' => $personalExpenseTotals,
        ];
    }

    /**
     * Get expenses in hierarchical structure (Super Category -> Category -> Items)
     */
    protected function getExpensesHierarchical(User $user, Carbon $startDate, Carbon $endDate, string $breakdownType, ?int $personId = null): array
    {
        $query = ExpenseEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate]);
        
        // Filter by person if specified (null person_id entries are included if personId is null)
        if ($personId !== null) {
            $query->where('person_id', $personId);
        }
        
        $entries = $query->with(['expenseCategory.expenseSuperCategory', 'person'])
            ->orderBy('date', 'desc')
            ->get();

        // Group by super category
        $grouped = $entries->groupBy(function ($entry) {
            return $entry->expenseCategory->expense_super_category_id;
        });

        $result = [];
        foreach ($grouped as $superCategoryId => $superCategoryEntries) {
            $firstEntry = $superCategoryEntries->first();
            $superCategory = $firstEntry->expenseCategory->expenseSuperCategory;
            $superCategoryTotal = $superCategoryEntries->sum('amount');
            $superCategoryCount = $superCategoryEntries->count();

            // Group by category
            $categoriesGrouped = $superCategoryEntries->groupBy('expense_category_id');
            $categories = [];

            foreach ($categoriesGrouped as $categoryId => $categoryEntries) {
                $firstCategoryEntry = $categoryEntries->first();
                $category = $firstCategoryEntry->expenseCategory;
                $categoryTotal = $categoryEntries->sum('amount');
                $categoryCount = $categoryEntries->count();

                // Only include categories if breakdown type is 'category' or 'item'
                if ($breakdownType === 'category' || $breakdownType === 'item') {
                    $categoryData = [
                        'id' => $categoryId,
                        'name' => $category->getTranslatedName(),
                        'emoji' => $category->emoji,
                        'total' => round($categoryTotal, 2),
                        'count' => $categoryCount,
                        'items' => [],
                    ];

                    // If breakdown type is 'item', include individual items
                    if ($breakdownType === 'item') {
                        $categoryData['items'] = $categoryEntries->map(function ($entry) {
                            return [
                                'date' => $entry->date->format('d/m/Y'),
                                'amount' => round($entry->amount, 2),
                                'notes' => $entry->notes,
                                'is_save_for_later' => $entry->is_save_for_later,
                                'is_personal' => $entry->is_personal,
                                'person' => $entry->person ? $entry->person->fullname : null,
                            ];
                        })->sortByDesc(function ($item) {
                            return $item['date'];
                        })->values()->toArray();
                    }

                    $categories[] = $categoryData;
                }
            }

            // Sort categories by total descending
            if (! empty($categories)) {
                usort($categories, function ($a, $b) {
                    return $b['total'] <=> $a['total'];
                });
            }

            $result[] = [
                'id' => $superCategoryId,
                'name' => $superCategory->getTranslatedName(),
                'emoji' => $superCategory->emoji,
                'total' => round($superCategoryTotal, 2),
                'count' => $superCategoryCount,
                'categories' => $categories,
            ];
        }

        // Sort super categories by total descending
        usort($result, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        return $result;
    }

    /**
     * Get income in hierarchical structure (Category -> Items)
     */
    protected function getIncomeHierarchical(User $user, Carbon $startDate, Carbon $endDate, string $breakdownType, ?int $personId = null): array
    {
        $query = IncomeEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate]);
        
        // Filter by person if specified (null person_id entries are included if personId is null)
        if ($personId !== null) {
            $query->where('person_id', $personId);
        }
        
        $entries = $query->with(['incomeCategory', 'person'])
            ->orderBy('date', 'desc')
            ->get();

        // Group by category
        $grouped = $entries->groupBy('income_category_id');

        $result = [];
        foreach ($grouped as $categoryId => $categoryEntries) {
            $firstEntry = $categoryEntries->first();
            $category = $firstEntry->incomeCategory;
            $categoryTotal = $categoryEntries->sum('amount');
            $categoryCount = $categoryEntries->count();

            $categoryData = [
                'id' => $categoryId,
                'name' => $category->getTranslatedName(),
                'emoji' => $category->emoji,
                'total' => round($categoryTotal, 2),
                'count' => $categoryCount,
                'items' => [],
            ];

            // If breakdown type is 'item', include individual items
            if ($breakdownType === 'item') {
                $categoryData['items'] = $categoryEntries->map(function ($entry) {
                    return [
                        'date' => $entry->date->format('d/m/Y'),
                        'amount' => round($entry->amount, 2),
                        'notes' => $entry->notes,
                        'person' => $entry->person ? $entry->person->fullname : null,
                    ];
                })->sortByDesc(function ($item) {
                    return $item['date'];
                })->values()->toArray();
            }

            $result[] = $categoryData;
        }

        // Sort categories by total descending
        usort($result, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        return $result;
    }

    /**
     * Get income by category
     */
    protected function getIncomeByCategory(User $user, Carbon $startDate, Carbon $endDate): array
    {
        return IncomeEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->select('income_category_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('income_category_id')
            ->with('incomeCategory')
            ->get()
            ->map(function ($entry) {
                return [
                    'name' => $entry->incomeCategory->getTranslatedName(),
                    'emoji' => $entry->incomeCategory->emoji,
                    'total' => round($entry->total, 2),
                    'count' => $entry->count,
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->toArray();
    }

    /**
     * Get income by item (individual entries)
     */
    protected function getIncomeByItem(User $user, Carbon $startDate, Carbon $endDate): array
    {
        return IncomeEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('incomeCategory')
            ->orderBy('date', 'desc')
            ->orderBy('amount', 'desc')
            ->get()
            ->map(function ($entry) {
                return [
                    'date' => $entry->date->format('d/m/Y'),
                    'category' => $entry->incomeCategory->getTranslatedName(),
                    'emoji' => $entry->incomeCategory->emoji,
                    'amount' => round($entry->amount, 2),
                    'notes' => $entry->notes,
                ];
            })
            ->toArray();
    }

    /**
     * Get expenses by super category
     */
    protected function getExpensesBySuperCategory(User $user, Carbon $startDate, Carbon $endDate): array
    {
        return ExpenseEntry::where('expense_entries.user_id', $user->id)
            ->whereBetween('expense_entries.date', [$startDate, $endDate])
            ->join('expense_categories', 'expense_entries.expense_category_id', '=', 'expense_categories.id')
            ->join('expense_super_categories', 'expense_categories.expense_super_category_id', '=', 'expense_super_categories.id')
            ->select('expense_super_categories.id', 'expense_super_categories.name', 'expense_super_categories.emoji',
                DB::raw('SUM(expense_entries.amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('expense_super_categories.id', 'expense_super_categories.name', 'expense_super_categories.emoji')
            ->get()
            ->map(function ($entry) {
                return [
                    'name' => __("categories.expense_super.{$entry->name}"),
                    'emoji' => $entry->emoji,
                    'total' => round($entry->total, 2),
                    'count' => $entry->count,
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->toArray();
    }

    /**
     * Get expenses by category
     */
    protected function getExpensesByCategory(User $user, Carbon $startDate, Carbon $endDate): array
    {
        return ExpenseEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->select('expense_category_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('expense_category_id')
            ->with('expenseCategory.expenseSuperCategory')
            ->get()
            ->map(function ($entry) {
                return [
                    'name' => $entry->expenseCategory->getTranslatedName(),
                    'super_category' => $entry->expenseCategory->expenseSuperCategory->getTranslatedName(),
                    'emoji' => $entry->expenseCategory->emoji,
                    'total' => round($entry->total, 2),
                    'count' => $entry->count,
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->toArray();
    }

    /**
     * Get expenses by item (individual entries)
     */
    protected function getExpensesByItem(User $user, Carbon $startDate, Carbon $endDate): array
    {
        return ExpenseEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['expenseCategory.expenseSuperCategory'])
            ->orderBy('date', 'desc')
            ->orderBy('amount', 'desc')
            ->get()
            ->map(function ($entry) {
                return [
                    'date' => $entry->date->format('d/m/Y'),
                    'category' => $entry->expenseCategory->getTranslatedName(),
                    'super_category' => $entry->expenseCategory->expenseSuperCategory->getTranslatedName(),
                    'emoji' => $entry->expenseCategory->emoji,
                    'amount' => round($entry->amount, 2),
                    'notes' => $entry->notes,
                    'is_save_for_later' => $entry->is_save_for_later,
                ];
            })
            ->toArray();
    }

    /**
     * Calculate personal expense totals by person
     * Returns:
     * - total_spend: Expenses where is_personal = false
     * - personal_by_person: Array of [person_name => total] for expenses where is_personal = true
     */
    protected function calculatePersonalExpenseTotals(User $user, Carbon $startDate, Carbon $endDate): array
    {
        // Total Spend: expenses where is_personal = false
        $totalSpend = ExpenseEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('is_personal', false)
            ->sum('amount');

        // Get all persons for this user
        $persons = Person::where('user_id', $user->id)
            ->orderBy('fullname')
            ->get();

        // Calculate personal expenses by person
        $personalByPerson = [];
        foreach ($persons as $person) {
            $personalTotal = ExpenseEntry::where('user_id', $user->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('person_id', $person->id)
                ->where('is_personal', true)
                ->sum('amount');

            if ($personalTotal > 0) {
                $personalByPerson[$person->fullname] = round($personalTotal, 2);
            }
        }

        // Also include entries with no person (person_id is null) that are personal
        $noPersonPersonal = ExpenseEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNull('person_id')
            ->where('is_personal', true)
            ->sum('amount');

        if ($noPersonPersonal > 0) {
            $personalByPerson[__('common.no_person')] = round($noPersonPersonal, 2);
        }

        return [
            'total_spend' => round($totalSpend, 2),
            'personal_by_person' => $personalByPerson,
        ];
    }

    /**
     * Export data to CSV format
     */
    public function exportToCsv(array $data, string $filename = 'report.csv'): string
    {
        $csv = fopen('php://temp', 'r+');

        // Write headers
        if (! empty($data)) {
            $headers = array_keys(is_array($data[0]) ? $data[0] : $data);
            fputcsv($csv, $headers);
        }

        // Write data
        foreach ($data as $row) {
            if (is_array($row)) {
                fputcsv($csv, array_values($row));
            } else {
                fputcsv($csv, [$row]);
            }
        }

        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return $content;
    }
}
