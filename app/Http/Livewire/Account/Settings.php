<?php

namespace App\Http\Livewire\Account;

use App\Rules\NoProfanity;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Settings extends Component
{
    public $user;

    // Form fields
    public string $name = '';
    public ?string $publicAlias = null;
    public string $email = '';
    public string $originalEmail = '';
    public bool $publicProfile = false;
    public bool $showOnPublicLeaderboard = false;
    public bool $showNameOnPublicLeaderboard = false;
    public bool $allowReminderEmails = true;

    public $confirmEmailChange = false;

    public function getRules()
    {
        $rules = [
            'name'  => ['required'],
            'email' => ['required', 'email', 'unique:users,email,' . $this->user->id],
        ];

        // Apply profanity filter to public alias
        if ($this->publicAlias) {
            $rules['publicAlias'] = ['nullable', 'string', 'max:50', new NoProfanity];
        }

        // Apply profanity filter to name if showing on public leaderboard
        if ($this->showOnPublicLeaderboard && $this->showNameOnPublicLeaderboard) {
            $rules['name'] = ['required', new NoProfanity];
        }

        return $rules;
    }

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->name = $this->user->name;
        $this->publicAlias = $this->user->public_alias;
        $this->email = $this->user->email;
        $this->originalEmail = $this->user->email;
        $this->publicProfile = (bool) $this->user->public_profile;
        $this->showOnPublicLeaderboard = (bool) $this->user->show_on_public_leaderboard;
        $this->showNameOnPublicLeaderboard = (bool) $this->user->show_name_on_public_leaderboard;
        $this->allowReminderEmails = $this->user->allow_reminder_emails ?? true;
    }

    public function update()
    {
        $this->validate();

        // Check if email is being changed
        if ($this->email !== $this->originalEmail && !$this->confirmEmailChange) {
            $this->confirmEmailChange = true;
            return;
        }

        // Prevent enabling public leaderboard if account is too new
        if ($this->showOnPublicLeaderboard && !$this->user->canParticipateInPublicLeaderboard()) {
            $this->showOnPublicLeaderboard = false;
        }

        $this->user->name = $this->name;
        $this->user->public_alias = $this->publicAlias;
        $this->user->email = $this->email;
        $this->user->public_profile = $this->publicProfile;
        $this->user->show_on_public_leaderboard = $this->showOnPublicLeaderboard;
        $this->user->show_name_on_public_leaderboard = $this->showNameOnPublicLeaderboard;
        $this->user->allow_reminder_emails = $this->allowReminderEmails;
        $this->user->save();

        $this->originalEmail = $this->email;
        $this->confirmEmailChange = false;

        session()->flash('message', 'Settings saved.');

        return redirect()->to(route('account.settings'));
    }

    public function cancelEmailChange()
    {
        $this->email = $this->originalEmail;
        $this->confirmEmailChange = false;
    }

    public function render()
    {
        return view('livewire.account.settings');
    }
}
