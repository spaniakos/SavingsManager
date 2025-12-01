<?php

namespace App\Filament\Resources\ExpenseEntries\Pages;

use App\Filament\Resources\ExpenseEntries\ExpenseEntryResource;
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
            DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
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
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove expense_super_category_id as it's not a database field
        unset($data['expense_super_category_id']);
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
