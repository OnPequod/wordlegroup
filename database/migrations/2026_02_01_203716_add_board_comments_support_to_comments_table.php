<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // Make group_id nullable for public board comments
            $table->foreignId('group_id')->nullable()->change();

            // Add board_number for public board comments
            $table->unsignedInteger('board_number')->nullable()->after('group_id');
            $table->index(['board_number', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['board_number', 'created_at']);
            $table->dropColumn('board_number');
        });
    }
};
