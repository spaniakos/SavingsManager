<?php

namespace App\Filament\Widgets;

use App\Models\ExpenseCategory;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class SaveForLaterProgressWidget extends Widget
{
    protected string $view = 'filament.widgets.save-for-later-progress-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    public function getSaveForLaterCategories()
    {
        $userId = Auth::id();
        
        return ExpenseCategory::forUser($userId)
            ->whereNotNull('save_for_later_target')
            ->where('save_for_later_target', '>', 0)
            ->get()
            ->map(function ($category) {
                return [
                    'category' => $category,
                    'target' => $category->save_for_later_target,
                    'progress' => $category->getSaveForLaterProgress(),
                    'remaining' => $category->getRemainingSaveForLaterAmount(),
                    'frequency' => $category->save_for_later_frequency,
                    'amount' => $category->save_for_later_amount,
                ];
            });
    }
    
    protected static ?int $sort = 3;
}

