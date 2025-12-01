<?php

namespace App\Filament\Resources\ExpenseCategories\Pages;

use App\Filament\Resources\ExpenseCategories\ExpenseCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditExpenseCategory extends EditRecord
{
    protected static string $resource = ExpenseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->visible(fn () => !$this->record->is_system && $this->record->user_id === Auth::id())
                ->action(function () {
                    if ($this->record->is_system) {
                        \Filament\Notifications\Notification::make()
                            ->title(__('categories.cannot_delete_system'))
                            ->danger()
                            ->send();
                        return;
                    }
                    $this->record->delete();
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->record->is_system) {
            // Prevent editing system categories
            $this->form->disabled();
        }
        return $data;
    }
}
