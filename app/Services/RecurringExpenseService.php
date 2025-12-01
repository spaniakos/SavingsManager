<?php

namespace App\Services;

use App\Models\RecurringExpense;
use App\Models\ExpenseEntry;
use Carbon\Carbon;

class RecurringExpenseService
{
    /**
     * Generate expense entries for recurring expenses for a given month
     */
    public function generateExpensesForMonth(int $userId, ?Carbon $month = null): array
    {
        if (!$month) {
            $month = Carbon::now()->startOfMonth();
        } else {
            $month = $month->copy()->startOfMonth();
        }

        $recurringExpenses = RecurringExpense::where('user_id', $userId)
            ->where('is_active', true)
            ->where('start_date', '<=', $month->endOfMonth())
            ->where(function ($query) use ($month) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $month->startOfMonth());
            })
            ->get();

        $generated = [];

        foreach ($recurringExpenses as $recurring) {
            $dueDate = $this->calculateNextDueDate($recurring, $month);
            
            if ($dueDate && $dueDate->between($month->startOfMonth(), $month->endOfMonth())) {
                // Check if already generated for this period
                if (!$this->isAlreadyGenerated($recurring, $dueDate)) {
                    $entry = ExpenseEntry::create([
                        'user_id' => $userId,
                        'expense_category_id' => $recurring->expense_category_id,
                        'amount' => $recurring->amount,
                        'date' => $dueDate,
                        'notes' => __('common.recurring_expense_generated', ['name' => $recurring->expenseCategory->getTranslatedName()]),
                    ]);

                    $recurring->update(['last_generated_at' => now()]);
                    $generated[] = $entry;
                }
            }
        }

        return $generated;
    }

    /**
     * Get upcoming recurring expenses
     */
    public function getUpcomingRecurringExpenses(int $userId, int $daysAhead = 30): array
    {
        $recurringExpenses = RecurringExpense::where('user_id', $userId)
            ->where('is_active', true)
            ->get();

        $upcoming = [];

        foreach ($recurringExpenses as $recurring) {
            $nextDue = $this->calculateNextDueDate($recurring);
            if ($nextDue && $nextDue->lte(Carbon::now()->addDays($daysAhead))) {
                $upcoming[] = [
                    'recurring' => $recurring,
                    'next_due_date' => $nextDue,
                    'amount' => $recurring->amount,
                ];
            }
        }

        usort($upcoming, function ($a, $b) {
            return $a['next_due_date'] <=> $b['next_due_date'];
        });

        return $upcoming;
    }

    /**
     * Calculate next due date for a recurring expense
     */
    public function calculateNextDueDate(RecurringExpense $recurring, ?Carbon $fromDate = null): ?Carbon
    {
        if (!$fromDate) {
            $fromDate = Carbon::now();
        }

        $startDate = Carbon::parse($recurring->start_date);
        
        if ($fromDate->lt($startDate)) {
            return $startDate;
        }

        if ($recurring->end_date && $fromDate->gt(Carbon::parse($recurring->end_date))) {
            return null;
        }

        $nextDue = $startDate->copy();

        switch ($recurring->frequency) {
            case 'week':
                while ($nextDue->lte($fromDate)) {
                    $nextDue->addWeek();
                }
                break;
            case 'month':
                while ($nextDue->lte($fromDate)) {
                    $nextDue->addMonth();
                }
                break;
            case 'quarter':
                while ($nextDue->lte($fromDate)) {
                    $nextDue->addMonths(3);
                }
                break;
            case 'year':
                while ($nextDue->lte($fromDate)) {
                    $nextDue->addYear();
                }
                break;
        }

        if ($recurring->end_date && $nextDue->gt(Carbon::parse($recurring->end_date))) {
            return null;
        }

        return $nextDue;
    }

    /**
     * Check if expense was already generated for this period
     */
    protected function isAlreadyGenerated(RecurringExpense $recurring, Carbon $dueDate): bool
    {
        $periodStart = $dueDate->copy()->startOfMonth();
        $periodEnd = $dueDate->copy()->endOfMonth();

        return ExpenseEntry::where('user_id', $recurring->user_id)
            ->where('expense_category_id', $recurring->expense_category_id)
            ->where('amount', $recurring->amount)
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->exists();
    }
}

