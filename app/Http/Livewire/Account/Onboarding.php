<?php

namespace App\Http\Livewire\Account;

use App\Rules\NoProfanity;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Onboarding extends Component
{
    public $user;

    public string $name = '';
    public ?string $publicAlias = null;
    public string $email = '';
    public bool $publicProfile = false;
    public bool $showOnPublicLeaderboard = false;
    public bool $showNameOnPublicLeaderboard = false;
    public bool $allowReminderEmails = true;

    protected function rules(): array
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

        if ($this->user->hasCompletedOnboarding()) {
            $this->redirect(route('account.home'));
            return;
        }

        $this->name = $this->user->name;
        $this->publicAlias = $this->user->public_alias;
        $this->email = $this->user->email;
        $this->publicProfile = (bool) $this->user->public_profile;
        $this->showOnPublicLeaderboard = (bool) $this->user->show_on_public_leaderboard;
        $this->showNameOnPublicLeaderboard = (bool) $this->user->show_name_on_public_leaderboard;
        $this->allowReminderEmails = $this->user->allow_reminder_emails ?? true;
    }

    public function complete(): mixed
    {
        $this->validate();

        $user = Auth::user();

        // Prevent enabling public leaderboard if account is too new
        if ($this->showOnPublicLeaderboard && !$user->canParticipateInPublicLeaderboard()) {
            $this->showOnPublicLeaderboard = false;
        }

        $user->name = $this->name;
        $user->public_alias = $this->publicAlias;
        $user->email = $this->email;
        $user->public_profile = $this->publicProfile;
        $user->show_on_public_leaderboard = $this->showOnPublicLeaderboard;
        $user->show_name_on_public_leaderboard = $this->showNameOnPublicLeaderboard;
        $user->allow_reminder_emails = $this->allowReminderEmails;
        $user->save();

        $user->markOnboardingComplete();

        session()->flash('message', 'Welcome to Wordle Group!');

        return redirect()->to(route('account.home'));
    }

    public function render()
    {
        return view('livewire.account.onboarding');
    }
}
