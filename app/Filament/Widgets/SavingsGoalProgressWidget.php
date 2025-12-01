<?php

namespace App\Filament\Widgets;

use App\Models\SavingsGoal;
use App\Services\MilestoneNotificationService;
use App\Services\SavingsCalculatorService;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class SavingsGoalProgressWidget extends Widget
{
    protected string $view = 'filament.widgets.savings-goal-progress-widget';

    protected int|string|array $columnSpan = 'full';

    protected SavingsCalculatorService $calculator;

    protected MilestoneNotificationService $milestoneService;

    public function mount(): void
    {
        $this->calculator = app(SavingsCalculatorService::class);
        $this->milestoneService = app(MilestoneNotificationService::class);

        // Check for milestones on mount
        $this->checkMilestones();
    }

    protected function checkMilestones(): void
    {
        $user = Auth::user();
        $notifications = $this->milestoneService->getMilestoneNotifications($user);

        foreach ($notifications as $notification) {
            if ($notification['type'] === 'completed') {
                Notification::make()
                    ->title($notification['message'])
                    ->success()
                    ->persistent()
                    ->send();
            } else {
                Notification::make()
                    ->title($notification['message'])
                    ->info()
                    ->send();
            }
        }
    }

    public function getGoalsProperty()
    {
        $userId = Auth::id();

        return SavingsGoal::where('user_id', $userId)
            ->orWhereHas('members', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->get();
    }

    public function getProgressData(SavingsGoal $goal): array
    {
        return $this->calculator->getProgressData($goal, Auth::id());
    }

    public function getNetWorth()
    {
        $user = Auth::user();

        return $user->net_worth;
    }

    public function getSeedCapital()
    {
        $user = Auth::user();

        return $user->seed_capital ?? 0;
    }

    protected static ?int $sort = 1;
}
