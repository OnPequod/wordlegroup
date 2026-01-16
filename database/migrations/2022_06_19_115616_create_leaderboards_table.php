<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaderboards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->on('groups');
            $table->string('for')->index();
            $table->year('year')->nullable()->index();
            $table->tinyInteger('month')->nullable()->index();
            $table->tinyInteger('week')->nullable()->index();
            $table->unsignedInteger('member_count')->default(0);
            $table->unsignedInteger('scores_recorded')->default(0);
            $table->float('score_mean')->nullable();
            $table->float('score_median')->nullable();
            $table->tinyInteger('score_mode')->nullable();
            $table->jsonb('score_distribution')->nullable();
            $table->jsonb('leaderboard')->nullable();
            $table->timestamps();
            $table->unique(['group_id', 'for', 'year', 'month', 'week']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leaderboards');
    }
};
