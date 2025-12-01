<?php

namespace App\Services;

use App\Models\SavingsGoal;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class MilestoneNotificationService
{
    protected SavingsCalculatorService $calculator;

    public function __construct(SavingsCalculatorService $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Check and notify about goal milestones
     */
    public function checkMilestones(User $user): void
    {
        $goals = SavingsGoal::where('user_id', $user->id)->get();

        foreach ($goals as $goal) {
            $this->checkGoalMilestones($goal, $user);
        }
    }

    /**
     * Check milestones for a specific goal
     */
    protected function checkGoalMilestones(SavingsGoal $goal, User $user): void
    {
        $progress = $this->calculator->calculateOverallProgress($goal);

        // Check for percentage milestones (25%, 50%, 75%, 100%)
        $milestones = [25, 50, 75, 100];

        foreach ($milestones as $milestone) {
            if ($progress >= $milestone && $progress < $milestone + 1) {
                $this->notifyMilestone($goal, $milestone, $user);
            }
        }

        // Check if goal is completed
        if ($progress >= 100 && $goal->current_amount >= $goal->target_amount) {
            $this->notifyGoalCompleted($goal, $user);
        }
    }

    /**
     * Notify about milestone reached
     */
    protected function notifyMilestone(SavingsGoal $goal, int $milestone, User $user): void
    {
        $message = __('common.milestone_reached', [
            'goal' => $goal->name,
            'milestone' => $milestone,
        ]);

        // Log milestone (can be extended to send email/notification)
        Log::info("Milestone reached: Goal {$goal->id} reached {$milestone}%", [
            'user_id' => $user->id,
            'goal_id' => $goal->id,
            'milestone' => $milestone,
        ]);
    }

    /**
     * Notify about goal completion
     */
    protected function notifyGoalCompleted(SavingsGoal $goal, User $user): void
    {
        $message = __('common.goal_completed', [
            'goal' => $goal->name,
        ]);

        Log::info("Goal completed: Goal {$goal->id}", [
            'user_id' => $user->id,
            'goal_id' => $goal->id,
        ]);
    }

    /**
     * Get milestone notifications for display
     */
    public function getMilestoneNotifications(User $user): array
    {
        $notifications = [];
        $goals = SavingsGoal::where('user_id', $user->id)->get();

        foreach ($goals as $goal) {
            $progress = $this->calculator->calculateOverallProgress($goal);

            if ($progress >= 100) {
                $notifications[] = [
                    'type' => 'completed',
                    'message' => __('common.goal_completed', ['goal' => $goal->name]),
                    'goal' => $goal,
                ];
            } elseif ($progress >= 75) {
                $notifications[] = [
                    'type' => 'milestone',
                    'message' => __('common.milestone_reached', ['goal' => $goal->name, 'milestone' => 75]),
                    'goal' => $goal,
                ];
            } elseif ($progress >= 50) {
                $notifications[] = [
                    'type' => 'milestone',
                    'message' => __('common.milestone_reached', ['goal' => $goal->name, 'milestone' => 50]),
                    'goal' => $goal,
                ];
            } elseif ($progress >= 25) {
                $notifications[] = [
                    'type' => 'milestone',
                    'message' => __('common.milestone_reached', ['goal' => $goal->name, 'milestone' => 25]),
                    'goal' => $goal,
                ];
            }
        }

        return $notifications;
    }
}
