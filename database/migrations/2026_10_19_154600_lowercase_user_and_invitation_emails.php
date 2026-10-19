<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Lowercase stored emails so they match the normalized form used on every
     * write and lookup. Users whose lowercased email would collide with another
     * account are left alone and logged, since they need a manual merge.
     */
    public function up(): void
    {
        $collisions = DB::table('users')
            ->selectRaw('lower(email) as email')
            ->groupByRaw('lower(email)')
            ->havingRaw('count(*) > 1')
            ->pluck('email');

        DB::table('users')
            ->whereRaw('email <> lower(email)')
            ->whereNotIn(DB::raw('lower(email)'), $collisions)
            ->update(['email' => DB::raw('lower(email)')]);

        if ($collisions->isNotEmpty()) {
            Log::warning('lowercase emails: users left unchanged because of case collisions', [
                'emails' => $collisions->all(),
            ]);
        }

        DB::table('group_membership_invitations')
            ->whereRaw('email <> lower(email)')
            ->update(['email' => DB::raw('lower(email)')]);
    }

    /**
     * The original casing is not recoverable.
     */
    public function down(): void
    {
    }
};
