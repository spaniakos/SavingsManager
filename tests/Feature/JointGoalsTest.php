<?php

namespace Tests\Feature;

use App\Models\SavingsGoal;
use App\Models\SavingsContribution;
use App\Models\User;
use App\Services\JointGoalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JointGoalsTest extends TestCase
{
    use RefreshDatabase;

    protected JointGoalService $jointGoalService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jointGoalService = new JointGoalService();
    }

    public function test_user_can_create_joint_goal(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $goal = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Joint Vacation Goal',
            'target_amount' => 5000.00,
            'current_amount' => 0.00,
            'initial_checkpoint' => 0.00,
            'start_date' => now(),
            'target_date' => now()->addMonths(6),
            'is_joint' => true,
        ]);

        $this->assertTrue($goal->is_joint);
        $this->assertDatabaseHas('savings_goals', [
            'id' => $goal->id,
            'is_joint' => true,
        ]);
    }

    public function test_owner_can_invite_member_to_joint_goal(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $goal = SavingsGoal::factory()->create([
            'user_id' => $owner->id,
            'is_joint' => true,
        ]);

        $result = $this->jointGoalService->inviteMember($goal, $member->email, $owner->id);

        $this->assertTrue($result);
        $this->assertDatabaseHas('savings_goal_members', [
            'savings_goal_id' => $goal->id,
            'user_id' => $member->id,
            'status' => 'pending',
        ]);
    }

    public function test_member_can_accept_invitation(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $goal = SavingsGoal::factory()->create([
            'user_id' => $owner->id,
            'is_joint' => true,
        ]);

        // Invite member
        $inviteResult = $this->jointGoalService->inviteMember($goal, $member->email, $owner->id);
        $this->assertTrue($inviteResult, 'Member should be invited successfully');

        // Refresh goal to reload relationships
        $goal->refresh();

        // Accept invitation
        $result = $this->jointGoalService->acceptInvitation($goal, $member->id);

        $this->assertTrue($result);
        $this->assertDatabaseHas('savings_goal_members', [
            'savings_goal_id' => $goal->id,
            'user_id' => $member->id,
            'status' => 'accepted',
        ]);
    }

    public function test_member_can_decline_invitation(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $goal = SavingsGoal::factory()->create([
            'user_id' => $owner->id,
            'is_joint' => true,
        ]);

        // Invite member
        $this->jointGoalService->inviteMember($goal, $member->email, $owner->id);

        // Decline invitation
        $result = $this->jointGoalService->declineInvitation($goal, $member->id);

        $this->assertTrue($result);
        $this->assertDatabaseHas('savings_goal_members', [
            'savings_goal_id' => $goal->id,
            'user_id' => $member->id,
            'status' => 'declined',
        ]);
    }

    public function test_owner_can_edit_joint_goal(): void
    {
        $owner = User::factory()->create();
        $goal = SavingsGoal::factory()->create([
            'user_id' => $owner->id,
            'is_joint' => true,
        ]);

        $canEdit = $this->jointGoalService->canEditGoal($goal, $owner->id);

        $this->assertTrue($canEdit);
    }

    public function test_accepted_member_can_contribute_to_goal(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $goal = SavingsGoal::factory()->create([
            'user_id' => $owner->id,
            'is_joint' => true,
            'current_amount' => 0.00,
        ]);

        // Invite and accept
        $inviteResult = $this->jointGoalService->inviteMember($goal, $member->email, $owner->id);
        $this->assertTrue($inviteResult, 'Member should be invited successfully');
        
        $goal->refresh();
        $acceptResult = $this->jointGoalService->acceptInvitation($goal, $member->id);
        $this->assertTrue($acceptResult, 'Invitation should be accepted successfully');

        // Check permissions
        $canContribute = $this->jointGoalService->canAddContributions($goal, $member->id);

        $this->assertTrue($canContribute);

        // Create contribution
        $contribution = SavingsContribution::create([
            'savings_goal_id' => $goal->id,
            'user_id' => $member->id,
            'amount' => 500.00,
            'date' => now(),
        ]);

        // Update goal amount
        $goal->refresh();
        $goal->increment('current_amount', $contribution->amount);

        $this->assertDatabaseHas('savings_contributions', [
            'savings_goal_id' => $goal->id,
            'user_id' => $member->id,
            'amount' => 500.00,
        ]);

        $goal->refresh();
        $this->assertEquals(500.00, $goal->current_amount);
    }

    public function test_pending_member_cannot_contribute(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $goal = SavingsGoal::factory()->create([
            'user_id' => $owner->id,
            'is_joint' => true,
        ]);

        // Invite but don't accept
        $this->jointGoalService->inviteMember($goal, $member->email, $owner->id);

        $canContribute = $this->jointGoalService->canAddContributions($goal, $member->id);

        $this->assertFalse($canContribute);
    }

    public function test_only_owner_can_invite_members(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $anotherUser = User::factory()->create();

        $goal = SavingsGoal::factory()->create([
            'user_id' => $owner->id,
            'is_joint' => true,
        ]);

        // Invite and accept member
        $this->jointGoalService->inviteMember($goal, $member->email, $owner->id);
        $this->jointGoalService->acceptInvitation($goal, $member->id);

        // Member cannot invite
        $canInvite = $this->jointGoalService->canInviteMembers($goal, $member->id);
        $this->assertFalse($canInvite);

        // Owner can invite
        $canInvite = $this->jointGoalService->canInviteMembers($goal, $owner->id);
        $this->assertTrue($canInvite);
    }
}

