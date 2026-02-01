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
        Schema::table('group_memberships', function (Blueprint $table) {
            $table->string('last_leaderboard_tab')->nullable()->after('last_viewed_activity_at');
        });
    }

    public function down(): void
    {
        Schema::table('group_memberships', function (Blueprint $table) {
            $table->dropColumn('last_leaderboard_tab');
        });
    }
};
