<?php

namespace App\Filament\Resources\SavingsGoals\Pages;

use App\Filament\Resources\SavingsGoals\SavingsGoalResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateSavingsGoal extends CreateRecord
{
    protected static string $resource = SavingsGoalResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }
    
    protected function afterCreate(): void
    {
        $members = $this->form->getState()['members'] ?? [];
        if (!empty($members)) {
            $this->record->members()->sync($members);
        }
    }
}
