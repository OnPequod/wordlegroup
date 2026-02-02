<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate any existing board comments to use polymorphic fields
        DB::table('comments')
            ->whereNotNull('board_number')
            ->update([
                'commentable_type' => 'board',
                'commentable_id' => DB::raw('board_number'),
            ]);

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['board_number', 'created_at']);
            $table->dropColumn('board_number');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedInteger('board_number')->nullable()->after('group_id');
            $table->index(['board_number', 'created_at']);
        });

        // Migrate back
        DB::table('comments')
            ->where('commentable_type', 'board')
            ->update([
                'board_number' => DB::raw('commentable_id'),
                'commentable_type' => null,
                'commentable_id' => null,
            ]);
    }
};
