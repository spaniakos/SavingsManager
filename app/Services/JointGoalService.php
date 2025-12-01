<?php

namespace App\Services;

use App\Models\SavingsGoal;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class JointGoalService
{
    /**
     * Invite a user to a joint goal by email
     */
    public function inviteMember(SavingsGoal $goal, string $email, int $invitedBy, string $role = 'member'): bool
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return false; // User not found
        }

        // Check if user is already a member
        if ($goal->members()->where('user_id', $user->id)->exists()) {
            return false; // Already a member
        }

        // Check if user is the goal owner
        if ($goal->user_id === $user->id) {
            return false; // Owner cannot be invited
        }

        // Add member with invitation details
        $goal->members()->attach($user->id, [
            'invited_by' => $invitedBy,
            'status' => 'pending',
            'invited_at' => now(),
            'role' => $role,
        ]);

        return true;
    }

    /**
     * Accept an invitation
     */
    public function acceptInvitation(SavingsGoal $goal, int $userId): bool
    {
        $member = $goal->members()->where('user_id', $userId)->first();
        
        if (!$member || $member->pivot->status !== 'pending') {
            return false;
        }

        $goal->members()->updateExistingPivot($userId, [
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return true;
    }

    /**
     * Decline an invitation
     */
    public function declineInvitation(SavingsGoal $goal, int $userId): bool
    {
        $member = $goal->members()->where('user_id', $userId)->first();
        
        if (!$member || $member->pivot->status !== 'pending') {
            return false;
        }

        $goal->members()->updateExistingPivot($userId, [
            'status' => 'declined',
        ]);

        return true;
    }

    /**
     * Check if user can edit goal
     */
    public function canEditGoal(SavingsGoal $goal, int $userId): bool
    {
        // Owner can always edit
        if ($goal->user_id === $userId) {
            return true;
        }

        // Admin members can edit
        $member = $goal->members()->where('user_id', $userId)->first();
        return $member && $member->pivot->role === 'admin' && $member->pivot->status === 'accepted';
    }

    /**
     * Check if user can add contributions
     */
    public function canAddContributions(SavingsGoal $goal, int $userId): bool
    {
        // Owner can always add
        if ($goal->user_id === $userId) {
            return true;
        }

        // Accepted members can add
        $member = $goal->members()->where('user_id', $userId)->first();
        return $member && $member->pivot->status === 'accepted';
    }

    /**
     * Check if user can invite members
     */
    public function canInviteMembers(SavingsGoal $goal, int $userId): bool
    {
        // Owner can always invite
        if ($goal->user_id === $userId) {
            return true;
        }

        // Admin members can invite
        $member = $goal->members()->where('user_id', $userId)->first();
        return $member && $member->pivot->role === 'admin' && $member->pivot->status === 'accepted';
    }

    /**
     * Get member contributions summary
     */
    public function getMemberContributions(SavingsGoal $goal): array
    {
        $contributions = $goal->contributions()
            ->select('user_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('user_id')
            ->with('user:id,name,email')
            ->get();

        return $contributions->map(function ($contribution) {
            return [
                'user_id' => $contribution->user_id,
                'user_name' => $contribution->user->name,
                'user_email' => $contribution->user->email,
                'total_contributed' => $contribution->total,
                'contribution_count' => $contribution->count,
            ];
        })->toArray();
    }
}

