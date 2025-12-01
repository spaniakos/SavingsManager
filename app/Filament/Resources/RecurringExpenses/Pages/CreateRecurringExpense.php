<?php

namespace App\Filament\Resources\RecurringExpenses\Pages;

use App\Filament\Resources\RecurringExpenses\RecurringExpenseResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateRecurringExpense extends CreateRecord
{
    protected static string $resource = RecurringExpenseResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        // Remove expense_super_category_id as it's not a database field
        unset($data['expense_super_category_id']);
        return $data;
    }
}
