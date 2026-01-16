<?php

namespace App\Http\Livewire\Group;

use App\Models\Group;
use App\Models\GroupMembershipInvitation;
use App\Rules\UserNotAlreadyMemberBeforeBeingInvited;
use Livewire\Component;

class InviteMember extends Component
{
    public $group;

    public $email;

    public $name;

    public function getRules()
    {
        return [
            'name' => 'required',
            'email' => [
                'required',
                'email',
                'unique:group_membership_invitations',
                new UserNotAlreadyMemberBeforeBeingInvited($this->group),
            ],
        ];
    }

    protected $messages = [
        'email.unique' => 'This user has already been invited. If they do not respond, you can invite them again in 24 hours.',
    ];

    public function mount(Group $group)
    {
        $this->group = $group;
    }

    public function invite()
    {
        $this->validate();

        GroupMembershipInvitation::createInvitation($this->group, $this->email, $this->name);

        session()->flash('message',
            $this->name . ' (' . $this->email . ') has been invited to join ' . $this->group->name . '.');

        return redirect()->to(route('group.home', $this->group));
    }

    public function render()
    {
        return view('livewire.group.invite-member');
    }
}
