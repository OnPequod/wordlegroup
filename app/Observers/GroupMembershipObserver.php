<?php

namespace App\Observers;

use App\Jobs\UpdateGroupStatsJob;
use App\Jobs\UpdateUserStatsJob;
use App\Models\GroupMembership;

class GroupMembershipObserver
{
    public function created(GroupMembership $membership): void
    {
        $this->runEvents($membership);
    }

    public function updated(GroupMembership $membership): void
    {
        // Skip expensive recalculation for non-score-related updates
        $ignoredFields = ['last_viewed_discussions_at', 'updated_at'];
        $changedFields = array_keys($membership->getChanges());
        $relevantChanges = array_diff($changedFields, $ignoredFields);

        if (empty($relevantChanges)) {
            return;
        }

        $this->runEvents($membership);
    }

    public function runEvents(GroupMembership $membership): void
    {
        UpdateUserStatsJob::dispatch($membership->user);
        UpdateGroupStatsJob::dispatchForUser($membership->user);
    }
}
