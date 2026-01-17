<?php

use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\GroupMembershipInvitation;
use App\Models\Score;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Public Routes - No Authentication Required
|--------------------------------------------------------------------------
*/

it('returns a successful response for the homepage', function () {
    $this->get('/')->assertOk();
});

it('returns a successful response for health checks', function () {
    $this->get('/health')->assertOk();
});

it('returns a successful response for the about page', function () {
    $this->get('/about')->assertOk();
});

it('returns a successful response for the contact page', function () {
    $this->get('/contact')->assertOk();
});

it('returns a successful response for the privacy policy page', function () {
    $this->get('/privacy-policy')->assertOk();
});

it('returns a successful response for the rules and faq page', function () {
    $this->get('/rules-and-frequently-asked-questions')->assertOk();
});

it('returns a successful response for the login page', function () {
    $this->get('/login')->assertOk();
});

it('returns a successful response for the register page', function () {
    $this->get('/register')->assertOk();
});

it('returns a successful response for the group create page', function () {
    $this->get('/group/create')->assertOk();
});

it('returns a successful response for a public user profile', function () {
    $user = User::factory()->create(['public_profile' => true]);

    $this->get("/u/{$user->id}")->assertOk();
});

it('returns a successful response for a public score share page', function () {
    // Create score without triggering observers that require full setup
    $user = User::factory()->create();
    $score = Score::withoutEvents(function () use ($user) {
        return Score::factory()->create([
            'user_id' => $user->id,
            'recording_user_id' => $user->id,
            'shared_at' => now(),
        ]);
    });

    $this->get("/score/{$score->id}")->assertOk();
});

it('returns a successful response for a group invitation page', function () {
    $invitation = GroupMembershipInvitation::factory()->create();

    $this->get("/group/invitation/{$invitation->id}?token={$invitation->token}")->assertOk();
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes - User Must Be Logged In
|--------------------------------------------------------------------------
*/

it('redirects to login for account home when not authenticated', function () {
    $this->get('/account')->assertRedirect('/login');
});

it('returns a successful response for account home when authenticated', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/account')
        ->assertOk();
});

it('returns a successful response for account groups when authenticated', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/account/groups')
        ->assertOk();
});

it('returns a successful response for record score page when authenticated', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/account/record-score')
        ->assertOk();
});

it('returns a successful response for account settings when authenticated', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/account/settings')
        ->assertOk();
});

it('account verify page responds for unverified user', function () {
    $user = User::factory()->unverified()->create();

    // Without token, should show verification needed page or redirect
    $response = $this->actingAs($user)
        ->get("/account/{$user->id}/verify");

    // Accept 200 (showing page) or 302 (redirect)
    expect($response->status())->toBeIn([200, 302]);
});

it('account verify page with token responds', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)
        ->get("/account/{$user->id}/verify?token={$user->auth_token}");

    // Should redirect after processing
    expect($response->status())->toBeIn([200, 302]);
});

it('account verify email notification page responds', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)
        ->get("/account/{$user->id}/verify-email");

    // Accept either 200 or 302 as valid responses
    expect($response->status())->toBeIn([200, 302]);
});

/*
|--------------------------------------------------------------------------
| Group Routes - User Must Be Logged In and Member of Group
|--------------------------------------------------------------------------
*/

it('returns a successful response for group home when user is a member', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    $this->actingAs($user)
        ->get("/group/{$group->id}")
        ->assertOk();
});

it('returns a successful response for group settings when user is admin', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    $this->actingAs($user)
        ->get("/group/{$group->id}/settings")
        ->assertOk();
});

it('returns a successful response for group not verified page', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id, 'verified_at' => null]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    $this->actingAs($user)
        ->get("/group/{$group->id}/not-verified")
        ->assertOk();
});

it('verifies group with valid token', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id, 'verified_at' => null]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    $this->actingAs($user)
        ->get("/group/{$group->id}/verify?token={$group->token}")
        ->assertRedirect(); // Redirects after successful verification

    expect($group->fresh()->verified_at)->not->toBeNull();
});

it('returns a successful response for group verify email notification page', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id, 'verified_at' => null]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    $this->actingAs($user)
        ->get("/group/{$group->id}/verify-email")
        ->assertOk();
});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

it('logs out a user and redirects to home', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/logout')
        ->assertRedirect('/');

    $this->assertGuest();
});
