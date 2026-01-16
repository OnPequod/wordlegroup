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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_id')->constrained()->on('users');
            $table->string('name');
            $table->unsignedInteger('member_count')->default(0);
            $table->unsignedInteger('scores_recorded')->default(0);
            $table->float('score_mean')->nullable();
            $table->float('score_median')->nullable();
            $table->tinyInteger('score_mode')->nullable();
            $table->jsonb('score_distribution')->nullable();
            $table->jsonb('leaderboard')->nullable();
            $table->string('token', 100)->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
};
