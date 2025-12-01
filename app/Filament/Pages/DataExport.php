<?php

namespace App\Filament\Pages;

use App\Services\ReportService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DataExport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    protected string $view = 'filament.pages.data-export';

    public static function getNavigationLabel(): string
    {
        return __('common.data_export');
    }

    public ?array $data = [];

    protected ReportService $reportService;

    public function mount(): void
    {
        $this->reportService = app(ReportService::class);
        $this->form->fill([
            'export_type' => 'all',
            'start_date' => now()->startOfYear()->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('export_type')
                    ->label(__('common.export_type'))
                    ->options([
                        'all' => __('common.all_data'),
                        'income' => __('common.income_entries'),
                        'expenses' => __('common.expense_entries'),
                        'savings_goals' => __('common.savings_goals'),
                    ])
                    ->required(),
                DatePicker::make('start_date')
                    ->label(__('common.start_date'))
                    ->displayFormat('d/m/Y')
                    ->required(),
                DatePicker::make('end_date')
                    ->label(__('common.end_date'))
                    ->displayFormat('d/m/Y')
                    ->required(),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_csv')
                ->label(__('common.export_csv'))
                ->icon('heroicon-o-arrow-down-tray')
                ->action('exportToCsv'),
            Action::make('export_json')
                ->label(__('common.export_json'))
                ->icon('heroicon-o-document-text')
                ->action('exportToJson'),
        ];
    }

    public function exportToCsv()
    {
        $data = $this->form->getState();
        $user = Auth::user();
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        $exportData = $this->prepareExportData($user, $data['export_type'], $startDate, $endDate);
        
        // Flatten data for CSV
        $csvData = [];
        foreach ($exportData as $type => $items) {
            foreach ($items as $item) {
                $csvData[] = array_merge(['type' => $type], $item);
            }
        }
        
        $csv = $this->reportService->exportToCsv($csvData);
        $filename = 'export_' . $data['export_type'] . '_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportToJson()
    {
        $data = $this->form->getState();
        $user = Auth::user();
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        $exportData = $this->prepareExportData($user, $data['export_type'], $startDate, $endDate);
        $filename = 'export_' . $data['export_type'] . '_' . now()->format('Y-m-d_His') . '.json';

        return response()->streamDownload(function () use ($exportData) {
            echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    protected function prepareExportData($user, string $type, Carbon $startDate, Carbon $endDate): array
    {
        $data = [];

        if ($type === 'all' || $type === 'income') {
            $data['income'] = \App\Models\IncomeEntry::where('user_id', $user->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->with('incomeCategory')
                ->get()
                ->map(fn ($entry) => [
                    'date' => $entry->date->format('Y-m-d'),
                    'category' => $entry->incomeCategory->getTranslatedName(),
                    'amount' => $entry->amount,
                    'notes' => $entry->notes,
                ])
                ->toArray();
        }

        if ($type === 'all' || $type === 'expenses') {
            $data['expenses'] = \App\Models\ExpenseEntry::where('user_id', $user->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->with('expenseCategory')
                ->get()
                ->map(fn ($entry) => [
                    'date' => $entry->date->format('Y-m-d'),
                    'category' => $entry->expenseCategory->getTranslatedName(),
                    'amount' => $entry->amount,
                    'notes' => $entry->notes,
                ])
                ->toArray();
        }

        if ($type === 'all' || $type === 'savings_goals') {
            $data['savings_goals'] = \App\Models\SavingsGoal::where('user_id', $user->id)
                ->get()
                ->map(fn ($goal) => [
                    'name' => $goal->name,
                    'target_amount' => $goal->target_amount,
                    'current_amount' => $goal->current_amount,
                    'start_date' => $goal->start_date->format('Y-m-d'),
                    'target_date' => $goal->target_date->format('Y-m-d'),
                    'is_joint' => $goal->is_joint,
                ])
                ->toArray();
        }

        return $data;
    }
}

