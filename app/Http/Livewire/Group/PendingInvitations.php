<?php

namespace App\Http\Livewire\Group;

use App\Mail\GroupInvitationReminder;
use App\Models\Group;
use App\Models\GroupMembershipInvitation;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class PendingInvitations extends Component
{
    public Group $group;

    public function mount(Group $group)
    {
        $this->group = $group;

        $this->group->pendingInvitations->load('group');
    }

    public function disinvite($invitationId)
    {
        $invitation = GroupMembershipInvitation::find($invitationId);

        $invitation->delete();

        session()->flash('message', $invitation->name . ' has been disinvited from the group.');

        return redirect()->to(route('group.home', $invitation->group));
    }

    public function remind($invitationId)
    {
        $invitation = GroupMembershipInvitation::find($invitationId);

        $invitation->sendReminderEmail();

        $invitation->update(['reminded_at' => now()]);

        session()->flash('message', $invitation->name . ' has been sent a reminder about how to join this group.');

        return redirect()->to(route('group.home', $invitation->group));
    }

    public function render()
    {
        return view('livewire.group.pending-invitations');
    }
}
