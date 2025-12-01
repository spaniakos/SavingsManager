<?php

namespace App\Filament\Widgets;

use App\Services\ChartDataService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class MoMSavingsChart extends ChartWidget
{
    protected ?string $heading = null;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return __('common.mom_comparison');
    }

    protected function getData(): array
    {
        $chartDataService = app(ChartDataService::class);
        $data = $chartDataService->getMoMSavingsComparison(6, Auth::id());

        return $chartDataService->formatForMoMChart($data);
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
