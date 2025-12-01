<?php

namespace App\Filament\Resources\ExpenseSuperCategories\Pages;

use App\Filament\Resources\ExpenseSuperCategories\ExpenseSuperCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExpenseSuperCategories extends ListRecords
{
    protected static string $resource = ExpenseSuperCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
    
}
