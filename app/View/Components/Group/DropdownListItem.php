<?php

namespace App\View\Components\Group;

use App\Models\GroupMembership;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class DropdownListItem extends Component
{
    public $groupMembership;

    public $user;

    public function __construct(GroupMembership $groupMembership, User $user = null)
    {
        $this->groupMembership = $groupMembership;
        $this->user = $user ?? Auth::user();
    }

    public function render()
    {
        return view('components.group.dropdown-list-item');
    }
}
