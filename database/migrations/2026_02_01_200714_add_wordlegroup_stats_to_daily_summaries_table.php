<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_summaries', function (Blueprint $table) {
            $table->unsignedInteger('wg_participant_count')->default(0)->after('participant_count');
            $table->float('wg_score_mean')->nullable()->after('wg_participant_count');
        });
    }

    public function down(): void
    {
        Schema::table('daily_summaries', function (Blueprint $table) {
            $table->dropColumn(['wg_participant_count', 'wg_score_mean']);
        });
    }
};
