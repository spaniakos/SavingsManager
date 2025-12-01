<?php

namespace App\Filament\Resources\IncomeCategories\Pages;

use App\Filament\Resources\IncomeCategories\IncomeCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateIncomeCategory extends CreateRecord
{
    protected static string $resource = IncomeCategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_system'] = false;
        $data['user_id'] = Auth::id();

        return $data;
    }
}
