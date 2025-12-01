<?php

namespace App\Filament\Resources\ExpenseEntries\Pages;

use App\Filament\Resources\ExpenseEntries\ExpenseEntryResource;
use App\Models\ExpenseEntry;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateExpenseEntry extends CreateRecord
{
    protected static string $resource = ExpenseEntryResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validate date is in current month
        $entryDate = Carbon::parse($data['date']);
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        
        if ($entryDate->lt($currentMonthStart) || $entryDate->gt($currentMonthEnd)) {
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                ['date' => [__('common.cannot_create_past_month_entry')]]
            );
        }
        
        $data['user_id'] = Auth::id();
        // Remove expense_super_category_id as it's not a database field
        unset($data['expense_super_category_id']);
        return $data;
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
