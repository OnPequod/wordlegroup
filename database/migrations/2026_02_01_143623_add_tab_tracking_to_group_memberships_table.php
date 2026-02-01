<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_memberships', function (Blueprint $table) {
            $table->string('last_active_tab')->nullable()->after('last_viewed_discussions_at');
            $table->timestamp('last_viewed_activity_at')->nullable()->after('last_active_tab');
        });
    }

    public function down(): void
    {
        Schema::table('group_memberships', function (Blueprint $table) {
            $table->dropColumn(['last_active_tab', 'last_viewed_activity_at']);
        });
    }
};
