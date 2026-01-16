<?php

namespace App\Http\Livewire\Account;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Settings extends Component
{
    public $user;

    public function getRules()
    {
        return [
            'user.name'  => ['required'],
            'user.email' => ['required', 'unique:users,email,' . $this->user->id],
            'user.public_profile' => [],
            'user.allow_digest_emails' => [],
            'user.allow_reminder_emails' => [],
        ];
    }

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function update()
    {
        $this->validate();

        $this->user->save();

        session()->flash('message', 'Settings saved.');

        return redirect()->to(route('account.settings'));
    }

    public function render()
    {
        return view('livewire.account.settings');
    }
}
