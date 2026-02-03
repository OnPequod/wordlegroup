<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset onboarding so existing users see new features
        DB::table('users')->update([
            'onboarding_completed_at' => null,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Mark all users as having completed onboarding
        DB::table('users')->whereNull('onboarding_completed_at')->update([
            'onboarding_completed_at' => now(),
        ]);
    }
};
