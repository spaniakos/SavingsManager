<?php

namespace App\Filament\Resources\ExpenseEntries;

use App\Filament\Resources\ExpenseEntries\Pages\CreateExpenseEntry;
use App\Filament\Resources\ExpenseEntries\Pages\EditExpenseEntry;
use App\Filament\Resources\ExpenseEntries\Pages\ListExpenseEntries;
use App\Filament\Resources\ExpenseEntries\Schemas\ExpenseEntryForm;
use App\Filament\Resources\ExpenseEntries\Tables\ExpenseEntriesTable;
use App\Models\ExpenseEntry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExpenseEntryResource extends Resource
{
    protected static ?string $model = ExpenseEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    
    public static function getNavigationLabel(): string
    {
        return __('common.expense_entries');
    }

    public static function form(Schema $schema): Schema
    {
        return ExpenseEntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExpenseEntriesTable::configure($table);
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
            'index' => ListExpenseEntries::route('/'),
            'create' => CreateExpenseEntry::route('/create'),
            'edit' => EditExpenseEntry::route('/{record}/edit'),
        ];
    }
}
