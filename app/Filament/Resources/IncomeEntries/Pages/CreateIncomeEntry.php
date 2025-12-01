<?php

namespace App\Filament\Resources\IncomeEntries\Pages;

use App\Filament\Resources\IncomeEntries\IncomeEntryResource;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateIncomeEntry extends CreateRecord
{
    protected static string $resource = IncomeEntryResource::class;
    
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
        return $data;
    }
}
