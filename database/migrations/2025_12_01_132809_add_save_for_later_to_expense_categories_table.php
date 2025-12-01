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
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->decimal('save_for_later_target', 10, 2)->nullable()->after('user_id');
            $table->enum('save_for_later_frequency', ['week', 'month', 'quarter', 'year'])->nullable()->after('save_for_later_target');
            $table->decimal('save_for_later_amount', 10, 2)->nullable()->after('save_for_later_frequency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropColumn(['save_for_later_target', 'save_for_later_frequency', 'save_for_later_amount']);
        });
    }
};
