<?php

namespace App\Filament\Resources\SavingsGoals\RelationManagers;

use App\Models\User;
use App\Services\JointGoalService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $title = 'Members';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->label(__('common.member_email'))
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->helperText(__('common.invite_by_email')),
                Select::make('role')
                    ->label(__('common.member_role'))
                    ->options([
                        'member' => __('common.member'),
                        'admin' => __('common.admin'),
                    ])
                    ->default('member')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('common.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('common.email'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pivot.status')
                    ->label(__('common.invitation_status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __("common.{$state}"))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'accepted',
                        'danger' => 'declined',
                    ]),
                TextColumn::make('pivot.role')
                    ->label(__('common.member_role'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __("common.{$state}")),
                TextColumn::make('pivot.invited_at')
                    ->label(__('common.invited_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('common.invitation_status'))
                    ->options([
                        'pending' => __('common.pending'),
                        'accepted' => __('common.accepted'),
                        'declined' => __('common.declined'),
                    ])
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            return $query->wherePivot('status', $data['value']);
                        }
                        return $query;
                    }),
            ])
            ->headerActions([
                Action::make('invite')
                    ->label(__('common.invite_member'))
                    ->form([
                        TextInput::make('email')
                            ->label(__('common.member_email'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Select::make('role')
                            ->label(__('common.member_role'))
                            ->options([
                                'member' => __('common.member'),
                                'admin' => __('common.admin'),
                            ])
                            ->default('member')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $service = app(JointGoalService::class);
                        $success = $service->inviteMember(
                            $this->getOwnerRecord(),
                            $data['email'],
                            Auth::id(),
                            $data['role']
                        );

                        if ($success) {
                            Notification::make()
                                ->title(__('common.invitation_sent'))
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title(__('common.user_not_found_or_already_member'))
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->recordActions([
                Action::make('accept')
                    ->label(__('common.accept'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->pivot->status === 'pending' && $record->id === Auth::id())
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $service = app(JointGoalService::class);
                        $success = $service->acceptInvitation($this->getOwnerRecord(), $record->id);

                        if ($success) {
                            Notification::make()
                                ->title(__('common.invitation_accepted'))
                                ->success()
                                ->send();
                        }
                    }),
                Action::make('decline')
                    ->label(__('common.decline'))
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->pivot->status === 'pending' && $record->id === Auth::id())
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $service = app(JointGoalService::class);
                        $success = $service->declineInvitation($this->getOwnerRecord(), $record->id);

                        if ($success) {
                            Notification::make()
                                ->title(__('common.invitation_declined'))
                                ->success()
                                ->send();
                        }
                    }),
                DetachAction::make()
                    ->visible(fn ($record) => 
                        $this->getOwnerRecord()->user_id === Auth::id() || 
                        ($record->pivot->role === 'admin' && $record->pivot->status === 'accepted')
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => $this->getOwnerRecord()->user_id === Auth::id()),
                ]),
            ]);
    }
}
