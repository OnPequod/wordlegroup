<?php

use App\Http\Livewire\Account\Login;
use App\Http\Livewire\Account\Register;
use App\Http\Livewire\Account\PendingGroupInvitations;
use App\Http\Livewire\Group\CreateForm;
use App\Http\Livewire\Group\InviteMember;
use App\Http\Livewire\Group\MemberList;
use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\GroupMembershipInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

/*
|--------------------------------------------------------------------------
| Registration Tests
|--------------------------------------------------------------------------
*/

it('can register a new user', function () {
    Mail::fake();

    Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->call('store')
        ->assertRedirect();

    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
});

it('validates email format for registration', function () {
    Mail::fake();

    Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', 'not-a-valid-email')
        ->call('store')
        ->assertHasErrors(['email']);
});

it('validates email must be unique for registration', function () {
    Mail::fake();
    User::factory()->create(['email' => 'taken@example.com']);

    Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', 'taken@example.com')
        ->call('store')
        ->assertHasErrors(['email']);
});

it('validates name is required for registration', function () {
    Mail::fake();

    Livewire::test(Register::class)
        ->set('name', '')
        ->set('email', 'test@example.com')
        ->call('store')
        ->assertHasErrors(['name']);
});

/*
|--------------------------------------------------------------------------
| Login Tests
|--------------------------------------------------------------------------
*/

it('can send login code to existing user', function () {
    Mail::fake();
    $user = User::factory()->create(['email' => 'user@example.com']);

    Livewire::test(Login::class)
        ->set('email', 'user@example.com')
        ->call('send')
        ->assertSet('codeSent', true);

    expect($user->fresh()->login_code)->not->toBeNull();
});

it('can login with valid login code', function () {
    $user = User::factory()->create([
        'login_code' => '123456',
        'login_code_generated_at' => now(),
    ]);

    Livewire::test(Login::class)
        ->set('user', $user)
        ->set('codeSent', true)
        ->set('loginCode', '123456')
        ->call('attemptLoginWithCode')
        ->assertRedirect(route('account.home'));

    $this->assertAuthenticatedAs($user);
});

it('rejects invalid login code', function () {
    $user = User::factory()->create([
        'login_code' => '123456',
        'login_code_generated_at' => now(),
    ]);

    $component = Livewire::test(Login::class)
        ->set('user', $user)
        ->set('codeSent', true)
        ->set('loginCode', 'wrong-code')
        ->call('attemptLoginWithCode');

    // Should not redirect (stays on page)
    $this->assertGuest();
});

/*
|--------------------------------------------------------------------------
| Group Creation Tests
|--------------------------------------------------------------------------
*/

it('can create a new group', function () {
    Mail::fake();
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreateForm::class)
        ->set('groupName', 'My Test Group')
        ->set('email', $user->email)
        ->set('userName', $user->name)
        ->call('store')
        ->assertRedirect();

    $this->assertDatabaseHas('groups', ['name' => 'My Test Group']);
});

it('validates group name is required', function () {
    Mail::fake();
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreateForm::class)
        ->set('groupName', '')
        ->set('email', $user->email)
        ->call('store')
        ->assertHasErrors(['groupName']);
});

/*
|--------------------------------------------------------------------------
| Group Invitation Tests
|--------------------------------------------------------------------------
*/

it('can invite a member to a group', function () {
    Mail::fake();
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    Livewire::actingAs($user)
        ->test(InviteMember::class, ['group' => $group])
        ->set('name', 'Invited User')
        ->set('email', 'invited@example.com')
        ->call('invite')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('group_membership_invitations', [
        'email' => 'invited@example.com',
        'group_id' => $group->id,
    ]);
});

/*
|--------------------------------------------------------------------------
| Pending Group Invitations Tests
|--------------------------------------------------------------------------
*/

it('can accept a pending group invitation', function () {
    $user = User::factory()->create();
    $invitingUser = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $invitingUser->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $invitingUser->id, 'group_id' => $group->id]);

    $invitation = GroupMembershipInvitation::factory()->create([
        'group_id' => $group->id,
        'inviting_user_id' => $invitingUser->id,
        'email' => $user->email,
        'name' => $user->name,
    ]);

    Livewire::actingAs($user)
        ->test(PendingGroupInvitations::class, ['user' => $user])
        ->call('accept', $invitation->id);

    expect($user->memberships()->where('group_id', $group->id)->exists())->toBeTrue();
    $this->assertDatabaseMissing('group_membership_invitations', ['id' => $invitation->id]);
});

it('can decline a pending group invitation', function () {
    $user = User::factory()->create();
    $invitingUser = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $invitingUser->id]);

    $invitation = GroupMembershipInvitation::factory()->create([
        'group_id' => $group->id,
        'inviting_user_id' => $invitingUser->id,
        'email' => $user->email,
    ]);

    Livewire::actingAs($user)
        ->test(PendingGroupInvitations::class, ['user' => $user])
        ->call('decline', $invitation->id);

    $this->assertDatabaseMissing('group_membership_invitations', ['id' => $invitation->id]);
    expect($user->memberships()->where('group_id', $group->id)->exists())->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| Member List Tests
|--------------------------------------------------------------------------
*/

it('can remove a member from a group as admin', function () {
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $admin->id]);

    GroupMembership::factory()->create(['user_id' => $admin->id, 'group_id' => $group->id]);
    GroupMembership::factory()->create(['user_id' => $member->id, 'group_id' => $group->id]);

    Livewire::actingAs($admin)
        ->test(MemberList::class, ['group' => $group])
        ->call('remove', $member->id);

    expect($member->memberships()->where('group_id', $group->id)->exists())->toBeFalse();
});
