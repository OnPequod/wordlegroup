<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_leaderboards', function (Blueprint $table) {
            $table->id();
            $table->string('for')->index(); // 'forever', 'month', 'week'
            $table->year('year')->nullable()->index();
            $table->tinyInteger('month')->unsigned()->nullable()->index();
            $table->tinyInteger('week')->unsigned()->nullable()->index();
            $table->unsignedInteger('participant_count')->default(0);
            $table->float('score_mean')->nullable();
            $table->json('leaderboard')->nullable();
            $table->timestamps();
            $table->unique(['for', 'year', 'month', 'week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_leaderboards');
    }
};
