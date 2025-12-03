<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\UserStatsOverview;
use Filament\Pages\Page;

class AdminHome extends Page
{
    protected static ?string $navigationLabel = 'Home';
    
    protected static ?int $navigationSort = -1; // Make it appear first in navigation
    
    protected static ?string $title = 'Dashboard';
    
    public static function shouldRegisterNavigation(): bool
    {
        return true; // Show in navigation so it can be the first item and used as home
    }
    
    protected function getHeaderActions(): array
    {
        return [];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            UserStatsOverview::class,
        ];
    }
}
