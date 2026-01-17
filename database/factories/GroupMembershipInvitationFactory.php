<?php

namespace Database\Factories;

use App\Concerns\Tokens;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GroupMembershipInvitation>
 */
class GroupMembershipInvitationFactory extends Factory
{
    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'name' => $this->faker->name(),
            'group_id' => Group::factory(),
            'inviting_user_id' => User::factory(),
            'token' => app(Tokens::class)->generate(),
        ];
    }
}
