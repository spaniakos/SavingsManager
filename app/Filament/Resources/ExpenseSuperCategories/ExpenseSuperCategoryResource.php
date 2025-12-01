<?php

namespace App\Filament\Resources\ExpenseSuperCategories;

use App\Filament\Resources\ExpenseSuperCategories\Pages\CreateExpenseSuperCategory;
use App\Filament\Resources\ExpenseSuperCategories\Pages\EditExpenseSuperCategory;
use App\Filament\Resources\ExpenseSuperCategories\Pages\ListExpenseSuperCategories;
use App\Filament\Resources\ExpenseSuperCategories\Schemas\ExpenseSuperCategoryForm;
use App\Filament\Resources\ExpenseSuperCategories\Tables\ExpenseSuperCategoriesTable;
use App\Models\ExpenseSuperCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExpenseSuperCategoryResource extends Resource
{
    protected static ?string $model = ExpenseSuperCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    
    public static function getNavigationGroup(): ?string
    {
        return __('common.category_management');
    }
    
    public static function getNavigationLabel(): string
    {
        return __('common.expense_super_categories');
    }

    public static function form(Schema $schema): Schema
    {
        return ExpenseSuperCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExpenseSuperCategoriesTable::configure($table);
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
            'index' => ListExpenseSuperCategories::route('/'),
            // 'create' => CreateExpenseSuperCategory::route('/create'), // Disabled - super categories are fixed
            'edit' => EditExpenseSuperCategory::route('/{record}/edit'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false; // Super categories are fixed (essentials, lifestyle, savings)
    }
}
