<?php

namespace App\Concerns;

use App\Models\User;
use App\Services\AuthenticatedUserService;

class GetsUsersInSharedGroupsWithAuthenticatedUser
{
    public $user;

    public $users;

    public function __construct()
    {
        $this->user = app(AuthenticatedUserService::class)->get();
        $this->setUsers($this->user);
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function setUsers(User $user = null)
    {
        if (!$user) {
            $this->users = collect();

            return;
        }

        // User from AuthenticatedUserService already has memberships.group.memberships.user loaded
        $this->users = $user->memberships
            ->pluck('group')
            ->pluck('memberships')
            ->flatten()
            ->pluck('user');
    }
}
