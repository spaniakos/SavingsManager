<?php

namespace App\Filament\Resources\ExpenseSuperCategories\Pages;

use App\Filament\Resources\ExpenseSuperCategories\ExpenseSuperCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditExpenseSuperCategory extends EditRecord
{
    protected static string $resource = ExpenseSuperCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->visible(fn () => ! $this->record->is_system && $this->record->user_id === Auth::id())
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
        // Allow editing allocation_percentage even for system categories
        // Only the name field is disabled for system categories (handled in form schema)
        return $data;
    }
}
