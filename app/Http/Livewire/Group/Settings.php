<?php

namespace App\Http\Livewire\Group;

use App\Models\Group;
use App\Rules\TransferGroupAdministratorConfirmed;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Settings extends Component
{
    public Group $group;

    public string $name = '';
    public int $adminUserId;
    public bool $public = false;
    public int $initialAdminUserId;
    public bool $confirmTransfer = false;

    public function getRules()
    {
        return [
            'name'          => ['required'],
            'adminUserId'   => ['required'],
            'public'        => [],
            'confirmTransfer' => new TransferGroupAdministratorConfirmed($this->adminUserId, $this->initialAdminUserId),
        ];
    }

    public function mount(Group $group)
    {
        if (!$group->isAdmin(Auth::user())) {
            abort(403);
        }

        $this->group = $group;
        $this->name = $group->name;
        $this->adminUserId = $group->admin_user_id;
        $this->public = (bool) $group->public;
        $this->initialAdminUserId = $group->admin_user_id;
    }

    public function update()
    {
        $this->validate();

        $this->group->name = $this->name;
        $this->group->admin_user_id = $this->adminUserId;
        $this->group->public = $this->public;
        $this->group->save();

        if ($this->initialAdminUserId !== $this->adminUserId) {
            session()->flash('message',
                'Settings saved. ' . $this->group->fresh()->admin->name . ' is now the administrator of ' . $this->group->name . '.');

            return redirect()->to(route('group.home', $this->group));
        }

        session()->flash('message', 'Settings saved.');

        return redirect()->to(route('group.settings', $this->group));
    }

    public function render()
    {
        return view('livewire.group.settings');
    }
}
