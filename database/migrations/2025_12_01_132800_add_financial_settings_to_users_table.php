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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('seed_capital', 10, 2)->default(0)->after('password');
            $table->decimal('median_monthly_income', 10, 2)->nullable()->after('seed_capital');
            $table->date('income_last_verified_at')->nullable()->after('median_monthly_income');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['seed_capital', 'median_monthly_income', 'income_last_verified_at']);
        });
    }
};
