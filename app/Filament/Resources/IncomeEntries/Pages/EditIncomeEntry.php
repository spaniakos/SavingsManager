<?php

namespace App\Filament\Resources\IncomeEntries\Pages;

use App\Filament\Resources\IncomeEntries\IncomeEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Carbon\Carbon;

class EditIncomeEntry extends EditRecord
{
    protected static string $resource = IncomeEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->disabled(fn () => $this->isPastMonthEntry())
                ->tooltip(fn () => $this->isPastMonthEntry() ? __('common.cannot_delete_past_month_entry') : null),
        ];
    }
    
    protected function isPastMonthEntry(): bool
    {
        if (!$this->record || !$this->record->date) {
            return false;
        }
        
        $entryMonth = Carbon::parse($this->record->date)->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();
        
        // Entry is from a month before previous month (more than 1 month ago)
        return $entryMonth->lt($previousMonth);
    }
    
    protected function isPreviousMonthCalculated(): bool
    {
        $userId = Auth::id();
        $previousMonth = Carbon::now()->subMonth();
        $previousMonthEnd = $previousMonth->copy()->endOfMonth();
        
        $allGoals = \App\Models\SavingsGoal::where('user_id', $userId)->get();
        
        foreach ($allGoals as $goal) {
            if ($goal->last_monthly_calculation_at) {
                $lastCalc = Carbon::parse($goal->last_monthly_calculation_at);
                if ($lastCalc->isAfter($previousMonthEnd)) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Disable form if entry is from past month
        if ($this->isPastMonthEntry()) {
            $this->form->disabled();
        }
        
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Prevent editing past month entries
        if ($this->isPastMonthEntry()) {
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                ['date' => [__('common.cannot_edit_past_month_entry')]]
            );
        }
        
        // Check if previous month was calculated
        $previousMonthCalculated = $this->isPreviousMonthCalculated();
        $minDate = $previousMonthCalculated 
            ? Carbon::now()->startOfMonth() 
            : Carbon::now()->subMonth()->startOfMonth();
        $maxDate = Carbon::now()->endOfMonth();
        
        // Validate date is in allowed range
        $entryDate = Carbon::parse($data['date']);
        
        if ($entryDate->lt($minDate) || $entryDate->gt($maxDate)) {
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                ['date' => [$previousMonthCalculated 
                    ? __('common.cannot_create_past_month_entry') 
                    : __('common.can_only_create_current_or_previous_month')]]
            );
        }
        
        return $data;
    }
}
