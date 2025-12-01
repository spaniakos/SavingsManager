<?php

namespace Tests\Feature;

use App\Models\IncomeCategory;
use App\Models\IncomeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncomeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_income_entry(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Seed categories
        $this->artisan('db:seed', ['--class' => 'IncomeCategorySeeder']);
        $category = IncomeCategory::first();

        $incomeEntry = IncomeEntry::create([
            'user_id' => $user->id,
            'income_category_id' => $category->id,
            'amount' => 2000.00,
            'date' => now(),
            'notes' => 'Test income entry',
        ]);

        $this->assertDatabaseHas('income_entries', [
            'id' => $incomeEntry->id,
            'user_id' => $user->id,
            'amount' => 2000.00,
        ]);
    }

    public function test_user_can_only_see_own_income_entries(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->artisan('db:seed', ['--class' => 'IncomeCategorySeeder']);
        $category = IncomeCategory::first();

        IncomeEntry::create([
            'user_id' => $user1->id,
            'income_category_id' => $category->id,
            'amount' => 1000.00,
            'date' => now(),
        ]);

        IncomeEntry::create([
            'user_id' => $user2->id,
            'income_category_id' => $category->id,
            'amount' => 2000.00,
            'date' => now(),
        ]);

        $this->actingAs($user1);
        $user1Entries = IncomeEntry::where('user_id', $user1->id)->get();
        
        $this->assertCount(1, $user1Entries);
        $this->assertEquals(1000.00, $user1Entries->first()->amount);
    }

    public function test_user_can_update_income_entry(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->artisan('db:seed', ['--class' => 'IncomeCategorySeeder']);
        $category = IncomeCategory::first();

        $entry = IncomeEntry::create([
            'user_id' => $user->id,
            'income_category_id' => $category->id,
            'amount' => 1000.00,
            'date' => now(),
        ]);

        $entry->update(['amount' => 1500.00]);

        $this->assertDatabaseHas('income_entries', [
            'id' => $entry->id,
            'amount' => 1500.00,
        ]);
    }

    public function test_user_can_delete_income_entry(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->artisan('db:seed', ['--class' => 'IncomeCategorySeeder']);
        $category = IncomeCategory::first();

        $entry = IncomeEntry::create([
            'user_id' => $user->id,
            'income_category_id' => $category->id,
            'amount' => 1000.00,
            'date' => now(),
        ]);

        $entry->delete();

        $this->assertDatabaseMissing('income_entries', [
            'id' => $entry->id,
        ]);
    }
}
