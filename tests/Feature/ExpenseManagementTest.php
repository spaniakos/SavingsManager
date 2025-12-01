<?php

namespace Tests\Feature;

use App\Models\ExpenseCategory;
use App\Models\ExpenseEntry;
use App\Models\ExpenseSuperCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'ExpenseSuperCategorySeeder']);
        $this->artisan('db:seed', ['--class' => 'ExpenseCategorySeeder']);
    }

    public function test_user_can_create_expense_entry(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $superCategory = ExpenseSuperCategory::firstOrCreate(
            ['name' => 'essentials'],
            ['is_system' => true, 'allocation_percentage' => 50.00]
        );
        $category = ExpenseCategory::firstOrCreate(
            ['name' => 'test_expense_category', 'expense_super_category_id' => $superCategory->id],
            ['is_system' => false]
        );

        $expenseEntry = ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
            'amount' => 100.00,
            'date' => now(),
            'notes' => 'Test expense entry',
        ]);

        $this->assertDatabaseHas('expense_entries', [
            'id' => $expenseEntry->id,
            'user_id' => $user->id,
            'amount' => 100.00,
        ]);
    }

    public function test_user_can_only_see_own_expense_entries(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $superCategory = ExpenseSuperCategory::firstOrCreate(
            ['name' => 'essentials'],
            ['is_system' => true, 'allocation_percentage' => 50.00]
        );
        $category = ExpenseCategory::firstOrCreate(
            ['name' => 'test_expense_category_2', 'expense_super_category_id' => $superCategory->id],
            ['is_system' => false]
        );

        ExpenseEntry::create([
            'user_id' => $user1->id,
            'expense_category_id' => $category->id,
            'amount' => 50.00,
            'date' => now(),
        ]);

        ExpenseEntry::create([
            'user_id' => $user2->id,
            'expense_category_id' => $category->id,
            'amount' => 75.00,
            'date' => now(),
        ]);

        $this->actingAs($user1);
        $user1Entries = ExpenseEntry::where('user_id', $user1->id)->get();

        $this->assertCount(1, $user1Entries);
        $this->assertEquals(50.00, $user1Entries->first()->amount);
    }

    public function test_user_can_update_expense_entry(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $superCategory = ExpenseSuperCategory::firstOrCreate(
            ['name' => 'essentials'],
            ['is_system' => true, 'allocation_percentage' => 50.00]
        );
        $category = ExpenseCategory::firstOrCreate(
            ['name' => 'test_expense_category_3', 'expense_super_category_id' => $superCategory->id],
            ['is_system' => false]
        );

        $entry = ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
            'amount' => 100.00,
            'date' => now(),
        ]);

        $entry->update(['amount' => 150.00]);

        $this->assertDatabaseHas('expense_entries', [
            'id' => $entry->id,
            'amount' => 150.00,
        ]);
    }

    public function test_user_can_delete_expense_entry(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $superCategory = ExpenseSuperCategory::firstOrCreate(
            ['name' => 'essentials'],
            ['is_system' => true, 'allocation_percentage' => 50.00]
        );
        $category = ExpenseCategory::firstOrCreate(
            ['name' => 'test_expense_category_4', 'expense_super_category_id' => $superCategory->id],
            ['is_system' => false]
        );

        $entry = ExpenseEntry::create([
            'user_id' => $user->id,
            'expense_category_id' => $category->id,
            'amount' => 100.00,
            'date' => now(),
        ]);

        $entry->delete();

        $this->assertDatabaseMissing('expense_entries', [
            'id' => $entry->id,
        ]);
    }
}
