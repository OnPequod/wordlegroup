<?php

namespace App\Http\Livewire\Group;

use App\Models\Group;
use App\Models\User;
use App\Rules\TransferGroupAdministratorConfirmed;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Settings extends Component
{

    public $name;

    public $heading;

    public $public;

    public $group;

    public $initialAdminUserId;

    public $confirmTransfer;

    public function getRules()
    {
        return [
            'group.name'          => ['required'],
            'group.admin_user_id' => ['required'],
            'group.public'        => [],
            'confirmTransfer'     => new TransferGroupAdministratorConfirmed($this->group->admin_user_id,
                $this->initialAdminUserId),
        ];
    }

    public function mount(Group $group)
    {
        if (!$group->isAdmin(Auth::user())) {
            abort(403);
        }

        $this->group = $group;
        $this->heading = $group->name . ' Group Settings';

        $this->initialAdminUserId = $this->group->admin_user_id;
    }

    public function update()
    {
        $this->validate();

        $this->group->save();

        if ($this->initialAdminUserId !== $this->group->admin_user_id) {
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
