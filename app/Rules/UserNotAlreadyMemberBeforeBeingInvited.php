<?php

namespace App\Rules;

use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class UserNotAlreadyMemberBeforeBeingInvited implements Rule
{
    public $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    public function passes($attribute, $value)
    {
        $user = User::where('email', User::normalizeEmail($value))->first();

        if (!$user) {
            return true;
        }

        return GroupMembership::where('user_id', $user->id)
                              ->where('group_id', $this->group->id)
                              ->get()
                              ->isEmpty();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This user is already a member of this group.';
    }
}
