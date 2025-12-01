<?php

namespace App\Filament\Resources\ExpenseEntries\Pages;

use App\Filament\Resources\ExpenseEntries\ExpenseEntryResource;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExpenseEntry extends EditRecord
{
    protected static string $resource = ExpenseEntryResource::class;

    protected $previousSaveForLater = false;

    protected $previousAmount = 0;

    protected $previousWasSavingsCategory = false;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->disabled(fn () => $this->isPastMonthEntry())
                ->tooltip(fn () => $this->isPastMonthEntry() ? __('common.cannot_delete_past_month_entry') : null),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Prevent editing past month entries
        if ($this->isPastMonthEntry()) {
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                ['date' => [__('common.cannot_edit_past_month_entry')]]
            );
        }

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

        // Remove expense_super_category_id as it's not a database field
        unset($data['expense_super_category_id']);

        return $data;
    }

    protected function isPastMonthEntry(): bool
    {
        if (! $this->record || ! $this->record->date) {
            return false;
        }

        $entryMonth = Carbon::parse($this->record->date)->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();

        // Entry is from a month before previous month (more than 1 month ago)
        return $entryMonth->lt($previousMonth);
    }

    protected function isPreviousMonthCalculated(): bool
    {
        $userId = Auth::id();
        $previousMonth = Carbon::now()->subMonth();
        $previousMonthEnd = $previousMonth->copy()->endOfMonth();

        $allGoals = \App\Models\SavingsGoal::where('user_id', $userId)->get();

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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Disable form if entry is from past month
        if ($this->isPastMonthEntry()) {
            $this->form->disabled();
        }

        // Pre-populate expense_super_category_id from the category
        if (isset($data['expense_category_id']) && $this->record->expenseCategory) {
            $data['expense_super_category_id'] = $this->record->expenseCategory->expense_super_category_id;
        }
        // Store previous values for comparison
        $this->previousSaveForLater = $this->record->is_save_for_later ?? false;
        $this->previousAmount = $this->record->amount ?? 0;
        $this->previousWasSavingsCategory = $this->record->expenseCategory
            && $this->record->expenseCategory->expenseSuperCategory
            && $this->record->expenseCategory->expenseSuperCategory->name === 'savings';

        return $data;
    }

    protected function afterSave(): void
    {
        $currentSaveForLater = $this->record->is_save_for_later ?? false;
        $currentAmount = $this->record->amount ?? 0;
        $currentIsSavingsCategory = $this->record->expenseCategory
            && $this->record->expenseCategory->expenseSuperCategory
            && $this->record->expenseCategory->expenseSuperCategory->name === 'savings';

        // If it was save for later or Savings category before, remove the previous amount
        if ($this->previousSaveForLater || $this->previousWasSavingsCategory) {
            $this->removeFromSavingsGoals($this->previousAmount);
        }

        // If it's now save for later or Savings category, add the current amount
        if ($currentSaveForLater || $currentIsSavingsCategory) {
            $this->addToSavingsGoals($this->record);
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

    protected function removeFromSavingsGoals(float $amount): void
    {
        $user = $this->record->user;
        $goals = $user->savingsGoals()
            ->where('start_date', '<=', $this->record->date)
            ->where(function ($query) {
                $query->whereNull('target_date')
                    ->orWhere('target_date', '>=', $this->record->date);
            })
            ->get();

        foreach ($goals as $goal) {
            $goal->decrement('current_amount', $amount);
            // Allow negative values - don't clamp to 0
        }
    }
}
