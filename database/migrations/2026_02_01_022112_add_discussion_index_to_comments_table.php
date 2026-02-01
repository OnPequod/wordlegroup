<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // Index for discussion posts queries (whereNull commentable_type, whereNull parent_id, order by created_at)
            $table->index(['group_id', 'commentable_type', 'parent_id', 'created_at'], 'comments_discussion_posts_index');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex('comments_discussion_posts_index');
        });
    }
};
