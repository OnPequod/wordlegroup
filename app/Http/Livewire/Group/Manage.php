<?php

namespace App\Http\Livewire\Group;

use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Manage extends Component
{
    use AuthorizesRequests;

    public $key;

    public $group;

    public $admin;

    public function mount(Group $group)
    {
        $this->authorize($group);

        $this->group = $group->load('memberships.user');
    }

    public function authorize(Group $group)
    {
        if (!$group->verified()) {
            return redirect()->to(route('group.not-verified', $group));
        }
    }

    public function remove($membershipId)
    {
//        $user = GroupMembership::where('id', $membershipId);
//
//        $user->delete();
//
//        session()->flash('message', $user->name . ' renamed.');

    }

    public function render()
    {
        return view('livewire.group.manage');
    }
}
