<?php

namespace Tests\Feature;

use App\Models\SavingsGoal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavingsGoalTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_savings_goal(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $goal = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Test Goal',
            'target_amount' => 10000.00,
            'current_amount' => 2000.00,
            'initial_checkpoint' => 2000.00,
            'start_date' => now(),
            'target_date' => now()->addMonths(12),
            'is_joint' => false,
        ]);

        $this->assertDatabaseHas('savings_goals', [
            'id' => $goal->id,
            'user_id' => $user->id,
            'name' => 'Test Goal',
            'target_amount' => 10000.00,
            'current_amount' => 2000.00,
        ]);
    }

    public function test_user_can_only_see_own_savings_goals(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        SavingsGoal::create([
            'user_id' => $user1->id,
            'name' => 'User 1 Goal',
            'target_amount' => 5000.00,
            'current_amount' => 1000.00,
            'initial_checkpoint' => 1000.00,
            'start_date' => now(),
            'target_date' => now()->addMonths(6),
            'is_joint' => false,
        ]);

        SavingsGoal::create([
            'user_id' => $user2->id,
            'name' => 'User 2 Goal',
            'target_amount' => 8000.00,
            'current_amount' => 2000.00,
            'initial_checkpoint' => 2000.00,
            'start_date' => now(),
            'target_date' => now()->addMonths(8),
            'is_joint' => false,
        ]);

        $this->actingAs($user1);
        $user1Goals = SavingsGoal::where('user_id', $user1->id)->get();
        
        $this->assertCount(1, $user1Goals);
        $this->assertEquals('User 1 Goal', $user1Goals->first()->name);
    }

    public function test_user_can_update_savings_goal(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $goal = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Test Goal',
            'target_amount' => 10000.00,
            'current_amount' => 2000.00,
            'initial_checkpoint' => 2000.00,
            'start_date' => now(),
            'target_date' => now()->addMonths(12),
            'is_joint' => false,
        ]);

        $goal->update(['current_amount' => 3000.00]);

        $this->assertDatabaseHas('savings_goals', [
            'id' => $goal->id,
            'current_amount' => 3000.00,
        ]);
    }

    public function test_user_can_delete_savings_goal(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $goal = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Test Goal',
            'target_amount' => 10000.00,
            'current_amount' => 2000.00,
            'initial_checkpoint' => 2000.00,
            'start_date' => now(),
            'target_date' => now()->addMonths(12),
            'is_joint' => false,
        ]);

        $goal->delete();

        $this->assertDatabaseMissing('savings_goals', [
            'id' => $goal->id,
        ]);
    }
}
