<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Cache;

trait GetsGroupData
{
    public function getGroupWithMemberships($group)
    {
        // Only load if not already loaded
        if (!$group->relationLoaded('memberships')) {
            $group->load(['memberships.user']);
        }

        return $group;
    }
}
