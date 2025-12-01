<?php

namespace App\Filament\Pages;

use App\Services\ReportService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Reports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected string $view = 'filament.pages.reports';

    public static function getNavigationLabel(): string
    {
        return __('common.reports');
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'report_type' => 'monthly',
            'month' => now()->format('Y-m'),
            'start_date' => now()->startOfMonth()->format('Y-m-d'),
            'end_date' => now()->endOfMonth()->format('Y-m-d'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('report_type')
                    ->label(__('common.report_type'))
                    ->options([
                        'monthly' => __('common.monthly_report'),
                        'category' => __('common.category_report'),
                        'savings' => __('common.savings_report'),
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('month', now()->format('Y-m'))),
                DatePicker::make('month')
                    ->label(__('common.month'))
                    ->displayFormat('F Y')
                    ->format('Y-m')
                    ->visible(fn (callable $get) => $get('report_type') === 'monthly')
                    ->required(fn (callable $get) => $get('report_type') === 'monthly'),
                DatePicker::make('start_date')
                    ->label(__('common.start_date'))
                    ->displayFormat('d/m/Y')
                    ->visible(fn (callable $get) => $get('report_type') === 'category')
                    ->required(fn (callable $get) => $get('report_type') === 'category'),
                DatePicker::make('end_date')
                    ->label(__('common.end_date'))
                    ->displayFormat('d/m/Y')
                    ->visible(fn (callable $get) => $get('report_type') === 'category')
                    ->required(fn (callable $get) => $get('report_type') === 'category'),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate')
                ->label(__('common.generate_report'))
                ->icon('heroicon-o-document-chart-bar')
                ->action('generateReport'),
            Action::make('export_csv')
                ->label(__('common.export_csv'))
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn () => !empty($this->reportData))
                ->action(function () {
                    if (empty($this->reportData)) {
                        return;
                    }

                    $reportService = app(ReportService::class);
                    $csv = $reportService->exportToCsv($this->reportData);
                    $filename = 'report_' . now()->format('Y-m-d_His') . '.csv';

                    return response()->streamDownload(function () use ($csv) {
                        echo $csv;
                    }, $filename, [
                        'Content-Type' => 'text/csv',
                    ]);
                }),
            Action::make('export_pdf')
                ->label(__('common.export_pdf'))
                ->icon('heroicon-o-document-arrow-down')
                ->visible(fn () => !empty($this->reportData))
                ->action(function () {
                    if (empty($this->reportData)) {
                        return;
                    }

                    return $this->exportToPdf();
                }),
        ];
    }

    public ?array $reportData = null;

    public function generateReport(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();
        $report = null;

        $reportService = app(ReportService::class);
        
        switch ($data['report_type']) {
            case 'monthly':
                $month = Carbon::createFromFormat('Y-m', $data['month']);
                $report = $reportService->generateMonthlyReport($user, $month);
                // Flatten summary for easier access in view
                if (isset($report['summary'])) {
                    $report['total_income'] = $report['summary']['total_income'];
                    $report['total_expenses'] = $report['summary']['total_expenses'];
                    $report['net_savings'] = $report['summary']['net_savings'];
                }
                break;
            case 'category':
                $startDate = Carbon::parse($data['start_date']);
                $endDate = Carbon::parse($data['end_date']);
                $report = $reportService->generateCategoryExpenseReport($user, $startDate, $endDate);
                // Flatten expenses_by_super_category for view
                if (isset($report['expenses_by_super_category'])) {
                    $expensesByCategory = [];
                    foreach ($report['expenses_by_super_category'] as $superCategory => $data) {
                        foreach ($data['categories'] as $category) {
                            $expensesByCategory[$category['name']] = $category['total'];
                        }
                    }
                    $report['expenses_by_category'] = $expensesByCategory;
                }
                $report['total_expenses'] = $report['total_expenses'] ?? 0;
                break;
            case 'savings':
                $report = $reportService->generateSavingsGoalReport($user);
                break;
        }

        if ($report) {
            $this->reportData = $report;
            Notification::make()
                ->title(__('common.report_generated_successfully'))
                ->success()
                ->send();
        }
    }

    protected function exportToPdf()
    {
        if (empty($this->reportData)) {
            return;
        }

        $data = $this->form->getState();
        $reportType = $data['report_type'] ?? 'monthly';
        
        $view = match($reportType) {
            'monthly' => 'filament.reports.monthly-pdf',
            'category' => 'filament.reports.category-pdf',
            'savings' => 'filament.reports.savings-pdf',
            default => 'filament.reports.monthly-pdf',
        };

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, [
                'reportData' => $this->reportData,
                'reportType' => $reportType,
                'user' => Auth::user(),
            ]);

            $filename = 'report_' . $reportType . '_' . now()->format('Y-m-d_His') . '.pdf';

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('common.pdf_export_error'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
