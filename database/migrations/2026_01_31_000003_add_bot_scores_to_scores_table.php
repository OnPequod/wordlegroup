<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->unsignedTinyInteger('bot_skill_score')->nullable()->after('hard_mode');
            $table->unsignedTinyInteger('bot_luck_score')->nullable()->after('bot_skill_score');
        });
    }

    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropColumn(['bot_skill_score', 'bot_luck_score']);
        });
    }
};
