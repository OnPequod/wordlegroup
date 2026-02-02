<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthenticatedUserService
{
    protected ?User $user = null;

    protected bool $loaded = false;

    public function get(): ?User
    {
        if (!Auth::check()) {
            return null;
        }

        if (!$this->loaded) {
            $this->user = Auth::user();
            $this->user->load([
                'memberships.group.admin',
                'memberships.group.memberships.user',
                'memberships.group.activeLeaderboards',
            ]);
            $this->loaded = true;
        }

        return $this->user;
    }

    public function check(): bool
    {
        return Auth::check();
    }
}
