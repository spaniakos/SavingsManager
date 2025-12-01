<?php

namespace App\Services;

use App\Models\User;
use App\Models\ExpenseSuperCategory;
use Carbon\Carbon;

class PositiveReinforcementService
{
    protected BudgetAllocationService $budgetService;

    public function __construct(BudgetAllocationService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    /**
     * Get encouragement messages for the user
     */
    public function getEncouragementMessages(User $user, ?Carbon $periodStart = null, ?Carbon $periodEnd = null): array
    {
        if (!$periodStart) {
            $periodStart = Carbon::now()->startOfMonth();
        }
        if (!$periodEnd) {
            $periodEnd = Carbon::now()->endOfMonth();
        }

        $messages = [];
        $daysRemaining = $this->getDaysRemainingInMonth();

        $allocationStatus = $this->budgetService->getAllocationStatus($user, $periodStart, $periodEnd);

        foreach ($allocationStatus as $status) {
            if (!$status['is_over_budget'] && $status['remaining'] > 0) {
                $message = $this->getSuperCategoryMessage($user, $status['super_category'], $periodStart, $periodEnd);
                if ($message) {
                    $messages[] = $message;
                }
            }
        }

        // Add general encouragement if under budget
        if (count($messages) > 0 && $daysRemaining > 0) {
            $totalRemaining = array_sum(array_column($allocationStatus, 'remaining'));
            if ($totalRemaining > 0) {
                $messages[] = [
                    'type' => 'general',
                    'message' => __('common.encouragement_days_remaining', [
                        'days' => $daysRemaining,
                        'amount' => number_format($totalRemaining, 2),
                    ]),
                ];
            }
        }

        return $messages;
    }

    /**
     * Get super category specific message
     */
    public function getSuperCategoryMessage(User $user, ExpenseSuperCategory $superCategory, Carbon $startDate, Carbon $endDate): ?array
    {
        $status = $this->budgetService->getAllocationStatus($user, $startDate, $endDate);
        $categoryStatus = collect($status)->firstWhere('super_category.id', $superCategory->id);

        if (!$categoryStatus || $categoryStatus['is_over_budget']) {
            return null;
        }

        $spentPercentage = $categoryStatus['spent_percentage'];
        $remaining = $categoryStatus['remaining'];

        if ($spentPercentage < 50) {
            return [
                'type' => 'excellent',
                'message' => __('common.encouragement_excellent', [
                    'category' => $superCategory->getTranslatedName(),
                    'spent' => number_format($categoryStatus['spent'], 2),
                    'allowance' => number_format($categoryStatus['allowance'], 2),
                ]),
            ];
        } elseif ($spentPercentage < 80) {
            return [
                'type' => 'good',
                'message' => __('common.encouragement_good', [
                    'category' => $superCategory->getTranslatedName(),
                    'spent' => number_format($categoryStatus['spent'], 2),
                    'allowance' => number_format($categoryStatus['allowance'], 2),
                    'remaining' => number_format($remaining, 2),
                ]),
            ];
        } elseif ($remaining > 0) {
            return [
                'type' => 'ok',
                'message' => __('common.encouragement_ok', [
                    'category' => $superCategory->getTranslatedName(),
                    'remaining' => number_format($remaining, 2),
                ]),
            ];
        }

        return null;
    }

    /**
     * Get days remaining in current month
     */
    public function getDaysRemainingInMonth(): int
    {
        $now = Carbon::now();
        $endOfMonth = $now->copy()->endOfMonth();
        return max(0, $now->diffInDays($endOfMonth));
    }
}

