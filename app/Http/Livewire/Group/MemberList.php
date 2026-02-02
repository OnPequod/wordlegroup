<?php

namespace App\Http\Livewire\Group;

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MemberList extends Component
{
    public $group;

    public function mount(Group $group)
    {
        if (!$group->relationLoaded('memberships')) {
            $group->load('memberships.user');
        }
        $this->group = $group;
    }

    public function nudge($userId)
    {
        $user = User::find($userId);

        $user->nudgeUser(Auth::user());

        session()->flash('message', $user->name . ' has been nudged to record their scores.');

        return redirect()->to(route('group.home', $this->group));
    }

    public function remove($userId)
    {
        $user = User::find($userId);

        $membership = $this->group->memberships()->where('user_id', $userId)->first();

        if($membership) {
            $membership->delete();
        }

        $this->group->fresh()->updateStats();

        session()->flash('message', $user->name . ' has been removed from the group.');

        return redirect()->to(route('group.home', $this->group));
    }

    public function render()
    {
        return view('livewire.group.member-list');
    }
}
