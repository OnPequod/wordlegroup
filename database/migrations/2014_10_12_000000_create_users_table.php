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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->boolean('dismissed_email_notification')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedInteger('daily_scores_recorded')->default(0);
            $table->float('daily_score_mean')->nullable();
            $table->float('daily_score_median')->nullable();
            $table->unsignedTinyInteger('daily_score_mode')->nullable();
            $table->jsonb('score_distribution')->nullable();
            $table->string('password')->nullable();
            $table->string('auth_token', 100)->nullable();
            $table->timestamp('auth_token_generated_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
