<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cookie;

class LanguageSwitcherWidget extends Widget
{
    protected string $view = 'filament.widgets.language-switcher-widget';

    protected int|string|array $columnSpan = 'full';

    public function switchLanguage(string $locale): void
    {
        if (in_array($locale, ['en', 'el'])) {
            Cookie::queue('locale', $locale, 60 * 24 * 365); // 1 year
            app()->setLocale($locale);

            \Filament\Notifications\Notification::make()
                ->title(__('common.language_changed'))
                ->success()
                ->send();

            redirect(request()->header('Referer') ?? '/admin');
        }
    }

    protected static ?int $sort = 0;
}
