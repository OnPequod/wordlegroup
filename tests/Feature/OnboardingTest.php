<?php

use App\Http\Livewire\Account\Onboarding;
use App\Models\User;
use Livewire\Livewire;

it('redirects users who need onboarding to the onboarding page', function () {
    $user = User::factory()->needsOnboarding()->create();

    $this->actingAs($user)
        ->get('/account')
        ->assertRedirect('/account/onboarding');
});

it('allows users who completed onboarding to access account pages', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/account')
        ->assertOk();
});

it('shows the onboarding page for users who need onboarding', function () {
    $user = User::factory()->needsOnboarding()->create();

    $this->actingAs($user)
        ->get('/account/onboarding')
        ->assertOk()
        ->assertSee('New Features');
});

it('redirects users who completed onboarding away from onboarding page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/account/onboarding')
        ->assertRedirect('/account');
});

it('completes onboarding and redirects to account home', function () {
    $user = User::factory()->needsOnboarding()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $this->actingAs($user);

    Livewire::test(Onboarding::class)
        ->set('name', 'Updated Name')
        ->set('email', 'test@example.com')
        ->set('publicProfile', true)
        ->set('showOnPublicLeaderboard', false)
        ->set('showNameOnPublicLeaderboard', false)
        ->set('allowReminderEmails', true)
        ->call('complete')
        ->assertRedirect('/account');

    $user->refresh();
    expect($user->name)->toBe('Updated Name')
        ->and($user->hasCompletedOnboarding())->toBeTrue()
        ->and($user->public_profile)->toBeTruthy();
});

it('allows logout without completing onboarding', function () {
    $user = User::factory()->needsOnboarding()->create();

    $this->actingAs($user)
        ->get('/logout')
        ->assertRedirect('/');

    $this->assertGuest();
});
