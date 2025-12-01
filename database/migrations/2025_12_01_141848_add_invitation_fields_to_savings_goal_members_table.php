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
        Schema::table('savings_goal_members', function (Blueprint $table) {
            $table->foreignId('invited_by')->nullable()->after('user_id')->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending')->after('invited_by');
            $table->timestamp('invited_at')->nullable()->after('status');
            $table->timestamp('accepted_at')->nullable()->after('invited_at');
            $table->enum('role', ['member', 'admin'])->default('member')->after('accepted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('savings_goal_members', function (Blueprint $table) {
            $table->dropForeign(['invited_by']);
            $table->dropColumn(['invited_by', 'status', 'invited_at', 'accepted_at', 'role']);
        });
    }
};
