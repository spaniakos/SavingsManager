<?php

namespace App\Filament\Resources\RecurringExpenses\Pages;

use App\Filament\Resources\RecurringExpenses\RecurringExpenseResource;
use App\Services\RecurringExpenseService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListRecurringExpenses extends ListRecords
{
    protected static string $resource = RecurringExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_expenses')
                ->label(__('common.generate_expenses'))
                ->icon('heroicon-o-sparkles')
                ->requiresConfirmation()
                ->action(function () {
                    $service = app(RecurringExpenseService::class);
                    $generated = $service->generateExpensesForMonth(Auth::id());
                    
                    \Filament\Notifications\Notification::make()
                        ->title(__('common.expenses_generated', ['count' => count($generated)]))
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
