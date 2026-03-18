<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scores', function (Blueprint $table): void {
            $table->index('board_number');
            $table->index(['user_id', 'board_number'], 'scores_user_id_board_number_index');
            $table->index(['recording_user_id', 'board_number'], 'scores_recording_user_id_board_number_index');
        });

        Schema::table('group_membership_score', function (Blueprint $table): void {
            $table->index(['group_id', 'board_number'], 'group_membership_score_group_id_board_number_index');
        });
    }

    public function down(): void
    {
        Schema::table('group_membership_score', function (Blueprint $table): void {
            $table->dropIndex('group_membership_score_group_id_board_number_index');
        });

        Schema::table('scores', function (Blueprint $table): void {
            $table->dropIndex(['board_number']);
            $table->dropIndex('scores_user_id_board_number_index');
            $table->dropIndex('scores_recording_user_id_board_number_index');
        });
    }
};
