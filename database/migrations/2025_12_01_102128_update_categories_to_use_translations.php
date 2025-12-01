<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing tables and recreate with new schema
        Schema::dropIfExists('expense_entries');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('expense_super_categories');
        Schema::dropIfExists('income_entries');
        Schema::dropIfExists('income_categories');
        
        // Recreate income_categories
        Schema::create('income_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_system')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->index(['user_id', 'is_system']);
        });
        
        // Recreate expense_super_categories
        Schema::create('expense_super_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_system')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->index(['user_id', 'is_system']);
        });
        
        // Recreate expense_categories
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('expense_super_category_id')->constrained()->onDelete('restrict');
            $table->boolean('is_system')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->index(['user_id', 'is_system']);
            $table->index('expense_super_category_id');
        });
        
        // Recreate income_entries
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
        
        // Recreate expense_entries
        Schema::create('expense_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('expense_category_id')->constrained()->onDelete('restrict');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'date']);
            $table->index('expense_category_id');
        });
    }

    public function down(): void
    {
        // This migration is destructive, so down() will just drop tables
        Schema::dropIfExists('expense_entries');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('expense_super_categories');
        Schema::dropIfExists('income_entries');
        Schema::dropIfExists('income_categories');
    }
};
