<?php

namespace App\Concerns;

use App\Models\User;
use App\Services\AuthenticatedUserService;
use Illuminate\Support\Facades\Auth;

class GetsUserGroupsWithRelationshipsLoaded
{
    public $user;

    public $groups;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->setGroups($user);
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function setGroups(User $user = null)
    {
        if (!$user) {
            $this->groups = collect();
            return;
        }

        // If this is the authenticated user, use the pre-loaded instance from the service
        $authUser = app(AuthenticatedUserService::class)->getWithGroupDetails();
        if ($authUser && $authUser->id === $user->id) {
            $this->user = $authUser;
            $this->groups = $authUser->memberships->pluck('group');
            return;
        }

        // For other users, load relationships only if not already loaded
        if (!$user->relationLoaded('memberships')) {
            $user->load(['memberships.group.memberships.user', 'memberships.group.activeLeaderboards']);
        }

        $this->groups = $user->memberships->pluck('group');
    }
}
