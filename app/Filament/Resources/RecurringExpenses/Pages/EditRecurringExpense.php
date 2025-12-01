<?php

namespace App\Filament\Resources\RecurringExpenses\Pages;

use App\Filament\Resources\RecurringExpenses\RecurringExpenseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRecurringExpense extends EditRecord
{
    protected static string $resource = RecurringExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Set expense_super_category_id based on the selected category for the form
        if (isset($data['expense_category_id']) && $this->record->expenseCategory) {
            $data['expense_super_category_id'] = $this->record->expenseCategory->expense_super_category_id;
        }
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove expense_super_category_id as it's not a database field
        unset($data['expense_super_category_id']);
        return $data;
    }
}
