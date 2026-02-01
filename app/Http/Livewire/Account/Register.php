<?php

namespace App\Http\Livewire\Account;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Register extends Component
{
    public $name;

    public $email;

    public $showOnPublicLeaderboard = false;

    public $showNameOnPublicLeaderboard = false;

    public function getRules()
    {
        return [
            'email' => [
                'email',
                'unique:users',
            ],
            'name'  => ['required'],
            'showOnPublicLeaderboard' => ['boolean'],
            'showNameOnPublicLeaderboard' => ['boolean'],
        ];
    }

    public function getMessages()
    {
        return [
            'email.unique' => 'This email has been taken. <a class="underline hover:text-red-800 font-semibold" href="' . route('login') . '">Go to the login page.</a>',
        ];
    }

    public function store()
    {
        $this->validate();

        $user = User::create([
            'email' => $this->email,
            'name'  => $this->name,
            'show_on_public_leaderboard' => $this->showOnPublicLeaderboard,
            'show_name_on_public_leaderboard' => $this->showNameOnPublicLeaderboard,
        ]);

        event(new Registered($user));

        return redirect()->to(route('account.verify-email-notification', $user));
    }

    public function render()
    {
        return view('livewire.account.register');
    }
}
