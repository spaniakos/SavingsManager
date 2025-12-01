<?php

namespace App\Filament\Resources\IncomeEntries\Pages;

use App\Filament\Resources\IncomeEntries\IncomeEntryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateIncomeEntry extends CreateRecord
{
    protected static string $resource = IncomeEntryResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }
}
