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

        UpdateGroupStatsJob::dispatchForUser($user);
    }
}
