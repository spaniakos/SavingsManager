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
        $currentMonth = Carbon::now()->startOfMonth();
        
        return $entryMonth->lt($currentMonth);
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
        
        return $data;
    }
}
