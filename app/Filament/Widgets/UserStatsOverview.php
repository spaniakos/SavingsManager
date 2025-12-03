<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->icon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Admins', User::where('is_admin', true)->count())
                ->icon('heroicon-m-shield-check')
                ->color('success'),

            Stat::make('Regular Users', User::where('is_admin', false)->count())
                ->icon('heroicon-m-user')
                ->color('gray'),
        ];
    }
}
