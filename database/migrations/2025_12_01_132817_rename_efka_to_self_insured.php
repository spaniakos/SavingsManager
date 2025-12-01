<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update expense categories with name 'efka' to 'self_insured'
        DB::table('expense_categories')
            ->where('name', 'efka')
            ->update(['name' => 'self_insured']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'self_insured' back to 'efka'
        DB::table('expense_categories')
            ->where('name', 'self_insured')
            ->update(['name' => 'efka']);
    }
};
