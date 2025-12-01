<?php

namespace App\Filament\Resources\SavingsGoals\RelationManagers;

use App\Services\JointGoalService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ContributionsRelationManager extends RelationManager
{
    protected static string $relationship = 'contributions';

    protected static ?string $title = 'Contributions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('amount')
                    ->label(__('common.amount'))
                    ->required()
                    ->numeric()
                    ->prefix('€')
                    ->step(0.01),
                DatePicker::make('date')
                    ->label(__('common.date'))
                    ->required()
                    ->default(now())
                    ->displayFormat('d/m/Y'),
                Textarea::make('notes')
                    ->label(__('common.notes'))
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('common.member'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label(__('common.amount'))
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('date')
                    ->label(__('common.date'))
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('notes')
                    ->label(__('common.notes'))
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('common.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        return $data;
                    })
                    ->after(function ($record) {
                        // Update goal current_amount
                        $goal = $this->getOwnerRecord();
                        $goal->increment('current_amount', $record->amount);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        return $data;
                    })
                    ->after(function ($record, array $data) {
                        // Update goal current_amount if amount changed
                        $goal = $this->getOwnerRecord();
                        $oldAmount = $record->getOriginal('amount');
                        $newAmount = $data['amount'];
                        $difference = $newAmount - $oldAmount;
                        $goal->increment('current_amount', $difference);
                    }),
                DeleteAction::make()
                    ->after(function ($record) {
                        // Decrease goal current_amount
                        $goal = $this->getOwnerRecord();
                        $goal->decrement('current_amount', $record->amount);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(function ($records) {
                            // Update goal current_amount for all deleted contributions
                            $goal = $this->getOwnerRecord();
                            $total = $records->sum('amount');
                            $goal->decrement('current_amount', $total);
                        }),
                ]),
            ]);
    }

    protected function canCreate(): bool
    {
        $service = app(JointGoalService::class);
        return $service->canAddContributions($this->getOwnerRecord(), Auth::id());
    }
}
