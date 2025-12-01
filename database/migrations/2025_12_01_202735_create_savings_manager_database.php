<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Income Categories
        Schema::create('income_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Translation key
            $table->string('emoji', 10)->nullable();
            $table->boolean('is_system')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['user_id', 'is_system']);
        });

        // Expense Super Categories (3-tier system: essentials, lifestyle, savings)
        Schema::create('expense_super_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Translation key
            $table->string('emoji', 10)->nullable();
            $table->decimal('allocation_percentage', 5, 2)->default(0);
            $table->boolean('is_system')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['user_id', 'is_system']);
        });

        // Expense Categories
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Translation key
            $table->string('emoji', 10)->nullable();
            $table->foreignId('expense_super_category_id')->constrained()->onDelete('restrict');
            $table->boolean('is_system')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('save_for_later_target', 10, 2)->nullable();
            $table->enum('save_for_later_frequency', ['week', 'month', 'quarter', 'year'])->nullable();
            $table->decimal('save_for_later_amount', 10, 2)->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_system']);
            $table->index('expense_super_category_id');
        });

        // Income Entries
        Schema::create('income_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('income_category_id')->constrained()->onDelete('restrict');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'date']);
            $table->index('income_category_id');
        });

        // Expense Entries
        Schema::create('expense_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('expense_category_id')->constrained()->onDelete('restrict');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->boolean('is_save_for_later')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'date']);
            $table->index('expense_category_id');
        });

        // Savings Goals
        Schema::create('savings_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('target_amount', 15, 2);
            $table->decimal('current_amount', 15, 2)->default(0);
            $table->decimal('initial_checkpoint', 10, 2)->default(0);
            $table->date('start_date');
            $table->date('target_date')->nullable();
            $table->timestamp('last_monthly_calculation_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id']);
            $table->index('target_date');
        });

        // Savings Goal Members (kept for future use, but not used in single-user mode)
        Schema::create('savings_goal_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('savings_goal_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('invited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->enum('role', ['member', 'admin'])->default('member');
            $table->timestamps();
            
            $table->unique(['savings_goal_id', 'user_id']);
            $table->index(['savings_goal_id', 'status']);
        });

        // Savings Contributions (kept for future use)
        Schema::create('savings_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('savings_goal_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['savings_goal_id', 'date']);
            $table->index('user_id');
        });

        // Recurring Expenses (kept for future use, but not actively used)
        Schema::create('recurring_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('expense_category_id')->constrained()->onDelete('restrict');
            $table->decimal('amount', 10, 2);
            $table->enum('frequency', ['week', 'month', 'quarter', 'year'])->default('month');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->dateTime('last_generated_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
            $table->index('expense_category_id');
        });

        // Category Allocation Goals
        Schema::create('category_allocation_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('expense_super_category_id')->constrained()->onDelete('cascade');
            $table->decimal('target_percentage', 5, 2);
            $table->date('period_start');
            $table->date('period_end')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'expense_super_category_id'], 'cat_alloc_goals_user_super_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_allocation_goals');
        Schema::dropIfExists('recurring_expenses');
        Schema::dropIfExists('savings_contributions');
        Schema::dropIfExists('savings_goal_members');
        Schema::dropIfExists('savings_goals');
        Schema::dropIfExists('expense_entries');
        Schema::dropIfExists('income_entries');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('expense_super_categories');
        Schema::dropIfExists('income_categories');
    }
};
