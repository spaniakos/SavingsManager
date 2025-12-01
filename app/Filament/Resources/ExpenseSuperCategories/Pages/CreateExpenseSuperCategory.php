<?php

namespace App\Filament\Resources\ExpenseSuperCategories\Pages;

use App\Filament\Resources\ExpenseSuperCategories\ExpenseSuperCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateExpenseSuperCategory extends CreateRecord
{
    protected static string $resource = ExpenseSuperCategoryResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_system'] = false;
        $data['user_id'] = Auth::id();
        return $data;
    }
}
