<?php

namespace App\Http\Livewire\Account;

use App\Mail\LoginEmail;
use App\Models\User;
use App\Rules\ValidLoginCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Login extends Component
{
    public $email;

    public $attempt;

    public $loginCode;

    public $codeSent;

    public $codeResent = false;

    public $user;

    public function mount(Request $request)
    {
        if ($request->input('id') && $request->input('token')) {
            $this->attemptLogInThroughEmailLink($request);

            return;
        }

        if ($request->filled('email')) {
            $this->email = $request->input('email');
            $this->send();
        }
    }

    protected function attemptLogInThroughEmailLink($request)
    {
        $this->user = User::find($request->input('id'));

        if (!$this->user || !$this->user->validateAuthToken($request->input('token'))) {
            $this->addError('attempt', 'This log in link is no longer valid.');

            return;
        }

        return $this->logUserIn();
    }

    public function logUserIn()
    {
        Auth::loginUsingId($this->user->id, true);

        $this->user->resetAuthToken();
        $this->user->resetLoginCode();

        session()->flash('message', 'You have been logged in.');

        return redirect()->to(route('account.home'));
    }

    protected function getMessages()
    {
        return [
            'email.exists' => 'We could not find an account with that email address. <a class="underline hover:text-red-800 font-semibold" href="' . route('register') . '">Register an account.</a>',
        ];
    }

    public function attemptLoginWithCode()
    {
        $this->validate(['loginCode' => ['required', new ValidLoginCode($this->user)]]);

        return $this->logUserIn();
    }

    public function send()
    {
        $this->validate(['email'     => ['required_without:login_code', 'exists:users'],]);

        $this->user = User::where('email', $this->email)->first();

        $this->user->generateNewAuthToken();
        $this->user->generateNewLoginCode();

        Mail::to($this->user)
            ->send(new LoginEmail($this->user));

        $this->dispatch('login-code-sent');

        $this->codeSent = true;
    }

    public function sendAgain()
    {
        $this->send();

        $this->codeResent = true;
    }

    public function render()
    {
        return view('livewire.account.login');
    }
}
