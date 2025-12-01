<?php

namespace App\Filament\Resources\ExpenseCategories\Pages;

use App\Filament\Resources\ExpenseCategories\ExpenseCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateExpenseCategory extends CreateRecord
{
    protected static string $resource = ExpenseCategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_system'] = false;
        $data['user_id'] = Auth::id();

        return $data;
    }
}
