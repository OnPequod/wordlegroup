<?php

use App\Http\Livewire\Group\Settings;
use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\GroupMembershipInvitation;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

beforeEach(function () {
    Queue::fake();

    $this->group = Group::factory()->create(['name' => 'Smith, Family', 'verified_at' => now()]);
    $this->member = User::factory()->create();
    GroupMembership::factory()->create(['group_id' => $this->group->id, 'user_id' => $this->group->admin_user_id]);
    GroupMembership::factory()->create(['group_id' => $this->group->id, 'user_id' => $this->member->id]);
    GroupMembershipInvitation::factory()->create(['group_id' => $this->group->id]);
});

it('lets the admin delete a group once the name is typed', function () {
    $score = Score::factory()->create(['user_id' => $this->member->id]);

    Livewire::actingAs($this->group->admin)
        ->test(Settings::class, ['group' => $this->group])
        ->set('deleteConfirmation', 'Smith, Family')
        ->call('deleteGroup')
        ->assertHasNoErrors()
        ->assertRedirect(route('account.home'));

    expect(Group::find($this->group->id))->toBeNull()
        ->and(GroupMembership::where('group_id', $this->group->id)->count())->toBe(0)
        ->and(GroupMembershipInvitation::where('group_id', $this->group->id)->count())->toBe(0)
        ->and(Score::find($score->id))->not->toBeNull();

    $this->actingAs($this->member)->get(route('group.home', $this->group->id))->assertNotFound();
});

it('does not delete the group without the matching name', function () {
    Livewire::actingAs($this->group->admin)
        ->test(Settings::class, ['group' => $this->group])
        ->set('deleteConfirmation', 'Smith')
        ->call('deleteGroup')
        ->assertHasErrors('deleteConfirmation');

    expect(Group::find($this->group->id))->not->toBeNull();
});

it('does not let a non-admin open the settings to delete', function () {
    Livewire::actingAs($this->member)
        ->test(Settings::class, ['group' => $this->group])
        ->assertForbidden();
});
