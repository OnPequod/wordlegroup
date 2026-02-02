<?php

namespace App\Http\Livewire\Account;

use App\Services\AuthenticatedUserService;
use Livewire\Component;

class Home extends Component
{
    public $user;

    protected $listeners = ['scoreRecorded'];

    public function mount()
    {
        $this->user = app(AuthenticatedUserService::class)->get();
    }

    public function scoreRecorded()
    {
        session()->flash('message', 'Score recorded.');

        return redirect()->to(route('account.home'));
    }

    public function render()
    {
        return view('livewire.account.home');
    }
}
