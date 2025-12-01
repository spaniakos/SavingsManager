<?php

namespace App\Filament\Resources\ExpenseEntries\Pages;

use App\Filament\Resources\ExpenseEntries\ExpenseEntryResource;
use App\Models\ExpenseEntry;
use App\Models\SavingsGoal;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateExpenseEntry extends CreateRecord
{
    protected static string $resource = ExpenseEntryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Check if previous month was calculated
        $previousMonthCalculated = $this->isPreviousMonthCalculated();
        $minDate = $previousMonthCalculated
            ? Carbon::now()->startOfMonth()
            : Carbon::now()->subMonth()->startOfMonth();
        $maxDate = Carbon::now()->endOfMonth();

        // Validate date is in allowed range
        $entryDate = Carbon::parse($data['date']);

        if ($entryDate->lt($minDate) || $entryDate->gt($maxDate)) {
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                ['date' => [$previousMonthCalculated
                    ? __('common.cannot_create_past_month_entry')
                    : __('common.can_only_create_current_or_previous_month')]]
            );
        }

        $data['user_id'] = Auth::id();
        // Remove expense_super_category_id as it's not a database field
        unset($data['expense_super_category_id']);

        return $data;
    }

    protected function isPreviousMonthCalculated(): bool
    {
        $userId = Auth::id();
        $previousMonth = Carbon::now()->subMonth();
        $previousMonthEnd = $previousMonth->copy()->endOfMonth();

        $allGoals = SavingsGoal::where('user_id', $userId)->get();

        foreach ($allGoals as $goal) {
            if ($goal->last_monthly_calculation_at) {
                $lastCalc = Carbon::parse($goal->last_monthly_calculation_at);
                if ($lastCalc->isAfter($previousMonthEnd)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function afterCreate(): void
    {
        // Handle save for later - add to savings goals
        if ($this->record->is_save_for_later) {
            $this->addToSavingsGoals($this->record);
        }

        // Handle Savings category expenses - add to savings goals
        if ($this->record->expenseCategory && $this->record->expenseCategory->expenseSuperCategory) {
            if ($this->record->expenseCategory->expenseSuperCategory->name === 'savings') {
                $this->addToSavingsGoals($this->record);
            }
        }
    }

    protected function addToSavingsGoals(ExpenseEntry $entry): void
    {
        $user = $entry->user;
        $goals = $user->savingsGoals()
            ->where('start_date', '<=', $entry->date)
            ->where(function ($query) use ($entry) {
                $query->whereNull('target_date')
                    ->orWhere('target_date', '>=', $entry->date);
            })
            ->get();

        foreach ($goals as $goal) {
            $goal->increment('current_amount', $entry->amount);
        }
    }
}
