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
        Schema::create('daily_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('board_number')->unique()->index();
            $table->date('puzzle_date')->index();
            $table->unsignedInteger('participant_count')->default(0);
            $table->float('score_mean')->nullable();
            $table->float('score_median')->nullable();
            $table->json('score_distribution')->nullable();
            $table->float('bot_skill_mean')->nullable();
            $table->float('bot_luck_mean')->nullable();
            $table->float('difficulty_delta')->nullable();
            $table->float('all_time_mean')->nullable();
            $table->json('boards')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_summaries');
    }
};
