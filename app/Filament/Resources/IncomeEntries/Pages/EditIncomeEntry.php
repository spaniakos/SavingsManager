<?php

namespace App\Filament\Resources\IncomeEntries\Pages;

use App\Filament\Resources\IncomeEntries\IncomeEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIncomeEntry extends EditRecord
{
    protected static string $resource = IncomeEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
