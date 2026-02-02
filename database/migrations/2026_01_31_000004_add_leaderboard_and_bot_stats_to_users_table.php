<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('show_on_public_leaderboard')->default(false)->after('public_profile');
            $table->float('bot_skill_mean')->nullable()->after('score_distribution');
            $table->float('bot_luck_mean')->nullable()->after('bot_skill_mean');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['show_on_public_leaderboard', 'bot_skill_mean', 'bot_luck_mean']);
        });
    }
};
