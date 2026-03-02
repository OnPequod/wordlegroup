<?php

namespace App\Observers;

use App\Jobs\UpdateGroupStatsJob;
use App\Models\User;

class UserObserver
{
    public function saved(User $user): void
    {
        if (! $user->wasChanged(['name', 'public_alias'])) {
            return;
        }

        $user->memberships()->with('group')->get()->each(function ($membership): void {
            if ($membership->group) {
                UpdateGroupStatsJob::dispatch($membership->group);
            }
        });
    }
}
