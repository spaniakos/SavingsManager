<?php

namespace App\Services;

use App\Models\User;
use App\Models\ExpenseSuperCategory;
use Carbon\Carbon;

class BudgetAllocationService
{
    /**
     * Calculate super category allowance based on percentage of monthly income
     */
    public function calculateSuperCategoryAllowance(User $user, ExpenseSuperCategory $superCategory, ?float $monthlyIncome = null): float
    {
        if (!$monthlyIncome) {
            $monthlyIncome = $user->median_monthly_income ?? 0;
        }

        $percentage = $superCategory->allocation_percentage ?? 0;
        return (float) ($monthlyIncome * ($percentage / 100));
    }

    /**
     * Get total spent in a super category for a period
     */
    public function getSpentInSuperCategory(User $user, ExpenseSuperCategory $superCategory, Carbon $startDate, Carbon $endDate): float
    {
        return (float) $user->expenseEntries()
            ->whereHas('expenseCategory', function ($query) use ($superCategory) {
                $query->where('expense_super_category_id', $superCategory->id);
            })
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
    }

    /**
     * Get remaining allowance for a super category in a period
     */
    public function getRemainingAllowance(User $user, ExpenseSuperCategory $superCategory, Carbon $startDate, Carbon $endDate): float
    {
        $allowance = $this->calculateSuperCategoryAllowance($user, $superCategory);
        $spent = $this->getSpentInSuperCategory($user, $superCategory, $startDate, $endDate);
        return (float) max(0, $allowance - $spent);
    }

    /**
     * Get allocation status for all super categories
     */
    public function getAllocationStatus(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $superCategories = ExpenseSuperCategory::where('is_system', true)
            ->whereIn('name', ['essentials', 'lifestyle', 'savings'])
            ->get();

        $status = [];

        foreach ($superCategories as $superCategory) {
            $allowance = $this->calculateSuperCategoryAllowance($user, $superCategory);
            $spent = $this->getSpentInSuperCategory($user, $superCategory, $startDate, $endDate);
            $remaining = max(0, $allowance - $spent);
            $percentage = $allowance > 0 ? ($spent / $allowance) * 100 : 0;

            $status[] = [
                'super_category' => $superCategory,
                'allocation_percentage' => $superCategory->allocation_percentage,
                'allowance' => $allowance,
                'spent' => $spent,
                'remaining' => $remaining,
                'spent_percentage' => min(100, $percentage),
                'is_over_budget' => $spent > $allowance,
            ];
        }

        return $status;
    }
}

