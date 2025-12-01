<?php

namespace App\Filament\Resources\IncomeEntries;

use App\Filament\Resources\IncomeEntries\Pages\CreateIncomeEntry;
use App\Filament\Resources\IncomeEntries\Pages\EditIncomeEntry;
use App\Filament\Resources\IncomeEntries\Pages\ListIncomeEntries;
use App\Filament\Resources\IncomeEntries\Schemas\IncomeEntryForm;
use App\Filament\Resources\IncomeEntries\Tables\IncomeEntriesTable;
use App\Models\IncomeEntry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class IncomeEntryResource extends Resource
{
    protected static ?string $model = IncomeEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    
    protected static ?string $navigationLabel = null;
    
    public static function getNavigationLabel(): string
    {
        return __('common.income_entries');
    }

    public static function form(Schema $schema): Schema
    {
        return IncomeEntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IncomeEntriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIncomeEntries::route('/'),
            'create' => CreateIncomeEntry::route('/create'),
            'edit' => EditIncomeEntry::route('/{record}/edit'),
        ];
    }
}
