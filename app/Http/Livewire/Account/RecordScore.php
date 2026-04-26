<?php

namespace App\Http\Livewire\Account;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use function view;

class RecordScore extends Component
{
    public $user;

    public $group;

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function render()
    {
        return view('livewire.account.record-score');
    }
}
