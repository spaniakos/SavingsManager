<?php

namespace App\Filament\Pages;

use App\Models\Person;
use App\Services\ReportService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

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
            'start_date' => now()->startOfMonth()->format('Y-m-d'),
            'end_date' => now()->endOfMonth()->format('Y-m-d'),
            'breakdown_type' => 'super_category',
            'person_id' => null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('start_date')
                    ->label(__('common.from'))
                    ->displayFormat('d/m/Y')
                    ->required()
                    ->default(now()->startOfMonth()),
                DatePicker::make('end_date')
                    ->label(__('common.to'))
                    ->displayFormat('d/m/Y')
                    ->required()
                    ->default(now()->endOfMonth()),
                Select::make('breakdown_type')
                    ->label(__('common.breakdown_type'))
                    ->options([
                        'item' => __('common.per_item'),
                        'category' => __('common.per_category'),
                        'super_category' => __('common.per_super_category'),
                    ])
                    ->required()
                    ->default('super_category'),
                Select::make('person_id')
                    ->label(__('common.breakdown_by_person'))
                    ->options(function () {
                        $userId = Auth::id();
                        if (! $userId) {
                            return [];
                        }

                        $options = ['' => __('common.all_persons')];
                        $persons = Person::where('user_id', $userId)
                            ->orderBy('fullname')
                            ->get();

                        foreach ($persons as $person) {
                            $options[$person->id] = $person->fullname;
                        }

                        return $options;
                    })
                    ->searchable()
                    ->nullable()
                    ->default(null),
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
                ->visible(fn () => ! empty($this->reportData))
                ->action(function () {
                    if (empty($this->reportData)) {
                        return;
                    }

                    $reportService = app(ReportService::class);
                    $csv = $reportService->exportToCsv($this->reportData);
                    $filename = 'report_'.now()->format('Y-m-d_His').'.csv';

                    return response()->streamDownload(function () use ($csv) {
                        echo $csv;
                    }, $filename, [
                        'Content-Type' => 'text/csv',
                    ]);
                }),
            Action::make('export_pdf')
                ->label(__('common.export_pdf'))
                ->icon('heroicon-o-document-arrow-down')
                ->visible(fn () => ! empty($this->reportData))
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

        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $breakdownType = $data['breakdown_type'] ?? 'super_category';
        $personId = $data['person_id'] ?? null;

        $reportService = app(ReportService::class);

        // Generate comprehensive report with all breakdowns
        $report = $reportService->generateComprehensiveReport($user, $startDate, $endDate, $breakdownType, $personId);

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

        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('filament.reports.comprehensive-pdf', [
                'reportData' => $this->reportData,
                'startDate' => Carbon::parse($data['start_date'])->format('d/m/Y'),
                'endDate' => Carbon::parse($data['end_date'])->format('d/m/Y'),
                'breakdownType' => $data['breakdown_type'] ?? 'super_category',
                'personId' => $data['person_id'] ?? null,
                'user' => Auth::user(),
            ]);

            // Configure PDF options to enable remote images (for emoji support)
            $pdf->setOption('isRemoteEnabled', true);
            $pdf->setOption('isHtml5ParserEnabled', true);

            $filename = 'report_'.Carbon::parse($data['start_date'])->format('Y-m-d').'_to_'.Carbon::parse($data['end_date'])->format('Y-m-d').'.pdf';

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
