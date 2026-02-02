<?php

namespace App\Observers;

use App\Models\GroupMembership;

class GroupMembershipObserver
{
    public function created(GroupMembership $membership)
    {
        $this->runEvents($membership);
    }

    public function updated(GroupMembership $membership)
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

    public function runEvents(GroupMembership $membership)
    {
        $membership->user->updateStats();
        $membership->user->memberships->each(fn($membership) => $membership->group->updateStats());
    }
}
