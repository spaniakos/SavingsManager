<?php

namespace App\Services;

use App\Models\ExpenseEntry;
use App\Models\IncomeEntry;
use App\Models\SavingsGoal;
use Carbon\Carbon;

class SavingsCalculatorService
{
    /**
     * Calculate monthly saving needed to reach the goal
     *
     * @param SavingsGoal $goal
     * @return float
     */
    public function calculateMonthlySavingNeeded(SavingsGoal $goal): float
    {
        $monthsRemaining = $this->calculateMonthsRemaining($goal);
        
        if ($monthsRemaining <= 0) {
            return 0;
        }
        
        $currentAmount = $goal->current_amount ?? 0;
        $remainingAmount = $goal->target_amount - $currentAmount;
        
        if ($remainingAmount <= 0) {
            return 0;
        }
        
        return round($remainingAmount / $monthsRemaining, 2);
    }
    
    /**
     * Calculate months remaining until target date
     *
     * @param SavingsGoal $goal
     * @return int
     */
    public function calculateMonthsRemaining(SavingsGoal $goal): int
    {
        $now = Carbon::now();
        $targetDate = Carbon::parse($goal->target_date);
        
        if ($targetDate->isPast()) {
            return 0;
        }
        
        return max(0, $now->diffInMonths($targetDate) + ($targetDate->day >= $now->day ? 1 : 0));
    }
    
    /**
     * Calculate overall progress percentage (total saved vs total goal)
     *
     * @param SavingsGoal $goal
     * @return float
     */
    public function calculateOverallProgress(SavingsGoal $goal): float
    {
        if ($goal->target_amount <= 0) {
            return 0;
        }
        
        $currentAmount = $goal->current_amount ?? 0;
        $progress = ($currentAmount / $goal->target_amount) * 100;
        
        return min(100, max(0, round($progress, 2)));
    }
    
    /**
     * Calculate monthly progress percentage (current month savings vs monthly target)
     *
     * @param SavingsGoal $goal
     * @param int|null $userId
     * @return float
     */
    public function calculateMonthlyProgress(SavingsGoal $goal, ?int $userId = null): float
    {
        $monthlyTarget = $this->calculateMonthlySavingNeeded($goal);
        
        if ($monthlyTarget <= 0) {
            return 100; // Goal already reached or no time remaining
        }
        
        $currentMonthSavings = $this->calculateCurrentMonthSavings($goal, $userId);
        
        $progress = ($currentMonthSavings / $monthlyTarget) * 100;
        
        return min(100, max(0, round($progress, 2)));
    }
    
    /**
     * Calculate current month savings (income - expenses for current month)
     *
     * @param SavingsGoal $goal
     * @param int|null $userId
     * @return float
     */
    public function calculateCurrentMonthSavings(SavingsGoal $goal, ?int $userId = null): float
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        
        // Get user IDs for the goal (owner + members if joint)
        $userIds = $this->getGoalUserIds($goal, $userId);
        
        // Calculate total income for current month
        $totalIncome = IncomeEntry::whereIn('user_id', $userIds)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        
        // Calculate total expenses for current month
        // Note: save_for_later expenses count as expenses (reduce savings calculation)
        // but they also add to savings goals separately
        $totalExpenses = ExpenseEntry::whereIn('user_id', $userIds)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        
        return round($totalIncome - $totalExpenses, 2);
    }
    
    /**
     * Calculate projected savings if no more spending this month
     *
     * @param SavingsGoal $goal
     * @param int|null $userId
     * @return float
     */
    public function calculateProjectedMonthlySavings(SavingsGoal $goal, ?int $userId = null): float
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        
        // Get user IDs for the goal
        $userIds = $this->getGoalUserIds($goal, $userId);
        
        // Calculate total income for current month (including future dates)
        $totalIncome = IncomeEntry::whereIn('user_id', $userIds)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        
        // Calculate expenses only up to today (not future expenses)
        // Note: save_for_later expenses count as expenses (reduce savings calculation)
        // but they also add to savings goals separately
        $totalExpenses = ExpenseEntry::whereIn('user_id', $userIds)
            ->whereBetween('date', [$startOfMonth, $now])
            ->sum('amount');
        
        return round($totalIncome - $totalExpenses, 2);
    }
    
    /**
     * Get user IDs associated with the goal
     *
     * @param SavingsGoal $goal
     * @param int|null $userId
     * @return array
     */
    protected function getGoalUserIds(SavingsGoal $goal, ?int $userId = null): array
    {
        // Single user account - only return the goal owner
        $userIds = [$goal->user_id];
        
        // If specific userId provided, filter to that user only
        if ($userId !== null) {
            $userIds = array_intersect($userIds, [$userId]);
        }
        
        return array_unique($userIds);
    }
    
    /**
     * Get comprehensive progress data for a goal
     *
     * @param SavingsGoal $goal
     * @param int|null $userId
     * @return array
     */
    public function getProgressData(SavingsGoal $goal, ?int $userId = null): array
    {
        $monthsRemaining = $this->calculateMonthsRemaining($goal);
        $monthlySavingNeeded = $this->calculateMonthlySavingNeeded($goal);
        $overallProgress = $this->calculateOverallProgress($goal);
        $monthlyProgress = $this->calculateMonthlyProgress($goal, $userId);
        $currentMonthSavings = $this->calculateCurrentMonthSavings($goal, $userId);
        $projectedSavings = $this->calculateProjectedMonthlySavings($goal, $userId);
        
        return [
            'months_remaining' => $monthsRemaining,
            'monthly_saving_needed' => $monthlySavingNeeded,
            'overall_progress' => $overallProgress,
            'monthly_progress' => $monthlyProgress,
            'current_month_savings' => $currentMonthSavings,
            'projected_monthly_savings' => $projectedSavings,
            'remaining_amount' => max(0, $goal->target_amount - ($goal->current_amount ?? 0)),
            'current_amount' => $goal->current_amount ?? 0,
            'target_amount' => $goal->target_amount,
        ];
    }
}

