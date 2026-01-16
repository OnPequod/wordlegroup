<?php

namespace App\Http\Livewire\Group;

use App\Concerns\Tokens;
use App\Events\GroupCreated;
use App\Events\GroupMembershipCreated;
use App\Events\UnverifiedGroupCreated;
use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateForm extends Component
{
    public $email;

    public $groupName;

    public $userName;

    public $public;

    public $userPublicProfile;

    public $autofocus;

    public function mount($autofocus = false)
    {
        $this->autofocus = $autofocus;
    }

    public function getRules()
    {
        $rules = ['groupName' => 'required',];

        if (!Auth::check()) {
            $rules['email'] = ['required', 'email', 'unique:users',];
            $rules['userName'] = 'required';
        }

        return $rules;
    }

    public function getMessages()
    {
        return [
            'email.unique' => 'This email has been taken. <a class="underline hover:text-red-800 font-semibold" href="' . route('login') . '">Log in to proceed.</a>',
        ];
    }

    public function store()
    {
        $this->validate();

        $user = Auth::check()
            ? Auth::user()
            : User::firstOrCreate([
                'email'          => $this->email,
                'name'           => $this->userName,
                'public_profile' => $this->userPublicProfile ?? false,
            ]);

        $group = Group::create([
            'admin_user_id' => $user->id,
            'name'          => $this->groupName,
            'public'        => $this->public ?? false,
            'verified_at'   => Auth::check() ? now() : null,
            'token'         => Auth::check() ? null : app(Tokens::class)->generate(),
        ]);

        if (Auth::check()) {
            event(new GroupCreated($group));
        } else {
            event(new UnverifiedGroupCreated($group));
        }

        $groupMembership = GroupMembership::create([
            'group_id' => $group->id,
            'user_id'  => $user->id,
        ]);

        return Auth::check()
            ? redirect()->to(route('group.home', $group))
            : redirect()->to(route('group.verify-email-notification', $group));
    }

    public function render()
    {
        return view('livewire.group.create-form');
    }
}
