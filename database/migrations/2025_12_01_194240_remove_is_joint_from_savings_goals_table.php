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
        Schema::table('savings_goals', function (Blueprint $table) {
            $table->dropColumn('is_joint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('savings_goals', function (Blueprint $table) {
            $table->boolean('is_joint')->default(false)->after('target_date');
        });
    }
};
