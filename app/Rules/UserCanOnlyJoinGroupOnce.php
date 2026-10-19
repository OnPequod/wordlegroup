<?php

namespace App\Rules;

use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class UserCanOnlyJoinGroupOnce implements Rule
{
    public $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    public function passes($attribute, $value)
    {
        $user = User::where('email', User::normalizeEmail($value))->first();

        return $user ?
            GroupMembership::where([
                'group_id' => $this->group->id,
                'user_id'  => $user->id,
            ])->get()->isEmpty()
            : true;
    }

    public function message()
    {
        return 'Only use user per group.';
    }
}
