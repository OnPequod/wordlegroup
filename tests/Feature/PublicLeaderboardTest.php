<?php

use App\Concerns\UpdatesPublicLeaderboards;
use App\Models\PublicLeaderboard;
use App\Models\Score;
use App\Models\User;
use Livewire\Livewire;

/*
|--------------------------------------------------------------------------
| Public Leaderboard Route Tests
|--------------------------------------------------------------------------
*/

it('returns a successful response for the public leaderboard page', function () {
    $this->get('/leaderboard')->assertOk();
});

it('public leaderboard page shows period tabs', function () {
    $this->get('/leaderboard')
        ->assertOk()
        ->assertSee('All Time')
        ->assertSee('This Month')
        ->assertSee('This Week');
});

/*
|--------------------------------------------------------------------------
| Public Leaderboard Privacy Tests
|--------------------------------------------------------------------------
*/

it('only includes opted-in users in public leaderboard', function () {
    $optedInUser = User::factory()->create([
        'show_on_public_leaderboard' => true,
        'show_name_on_public_leaderboard' => true,
        'name' => 'Opted In User',
    ]);
    $optedOutUser = User::factory()->create([
        'show_on_public_leaderboard' => false,
        'name' => 'Opted Out User',
    ]);

    // Create scores for both users
    Score::withoutEvents(function () use ($optedInUser, $optedOutUser) {
        Score::factory()->create([
            'user_id' => $optedInUser->id,
            'recording_user_id' => $optedInUser->id,
        ]);
        Score::factory()->create([
            'user_id' => $optedOutUser->id,
            'recording_user_id' => $optedOutUser->id,
        ]);
    });

    // Sync to daily scores
    $optedInUser->dailyScores()->syncWithoutDetaching([
        $optedInUser->scores->first()->id => ['board_number' => $optedInUser->scores->first()->board_number],
    ]);
    $optedOutUser->dailyScores()->syncWithoutDetaching([
        $optedOutUser->scores->first()->id => ['board_number' => $optedOutUser->scores->first()->board_number],
    ]);

    // Update leaderboards
    app(UpdatesPublicLeaderboards::class)->update(now());

    $leaderboard = PublicLeaderboard::getForever();

    $userIds = $leaderboard->leaderboard->pluck('user_id')->toArray();

    expect($userIds)->toContain($optedInUser->id);
    expect($userIds)->not->toContain($optedOutUser->id);
});

it('uses public alias instead of name when set', function () {
    $userWithAlias = User::factory()->create([
        'show_on_public_leaderboard' => true,
        'show_name_on_public_leaderboard' => true,
        'name' => 'Real Name',
        'public_alias' => 'CoolAlias',
    ]);

    // Create score for user
    Score::withoutEvents(function () use ($userWithAlias) {
        Score::factory()->create([
            'user_id' => $userWithAlias->id,
            'recording_user_id' => $userWithAlias->id,
        ]);
    });

    // Sync to daily scores
    $userWithAlias->dailyScores()->syncWithoutDetaching([
        $userWithAlias->scores->first()->id => ['board_number' => $userWithAlias->scores->first()->board_number],
    ]);

    // Update leaderboards
    app(UpdatesPublicLeaderboards::class)->update(now());

    $leaderboard = PublicLeaderboard::getForever();

    $entry = $leaderboard->leaderboard->firstWhere('user_id', $userWithAlias->id);

    expect($entry['name'])->toBe('CoolAlias');
});

it('uses real name when no public alias is set', function () {
    $userWithoutAlias = User::factory()->create([
        'show_on_public_leaderboard' => true,
        'show_name_on_public_leaderboard' => true,
        'name' => 'My Real Name',
        'public_alias' => null,
    ]);

    // Create score for user
    Score::withoutEvents(function () use ($userWithoutAlias) {
        Score::factory()->create([
            'user_id' => $userWithoutAlias->id,
            'recording_user_id' => $userWithoutAlias->id,
        ]);
    });

    // Sync to daily scores
    $userWithoutAlias->dailyScores()->syncWithoutDetaching([
        $userWithoutAlias->scores->first()->id => ['board_number' => $userWithoutAlias->scores->first()->board_number],
    ]);

    // Update leaderboards
    app(UpdatesPublicLeaderboards::class)->update(now());

    $leaderboard = PublicLeaderboard::getForever();

    $entry = $leaderboard->leaderboard->firstWhere('user_id', $userWithoutAlias->id);

    expect($entry['name'])->toBe('My Real Name');
});

it('shows name only when user enables show_name_on_public_leaderboard', function () {
    $showNameUser = User::factory()->create([
        'show_on_public_leaderboard' => true,
        'show_name_on_public_leaderboard' => true,
        'name' => 'John Doe',
    ]);
    $hideNameUser = User::factory()->create([
        'show_on_public_leaderboard' => true,
        'show_name_on_public_leaderboard' => false,
        'name' => 'Jane Smith',
    ]);

    // Create scores for both users
    Score::withoutEvents(function () use ($showNameUser, $hideNameUser) {
        Score::factory()->create([
            'user_id' => $showNameUser->id,
            'recording_user_id' => $showNameUser->id,
        ]);
        Score::factory()->create([
            'user_id' => $hideNameUser->id,
            'recording_user_id' => $hideNameUser->id,
        ]);
    });

    // Sync to daily scores
    $showNameUser->dailyScores()->syncWithoutDetaching([
        $showNameUser->scores->first()->id => ['board_number' => $showNameUser->scores->first()->board_number],
    ]);
    $hideNameUser->dailyScores()->syncWithoutDetaching([
        $hideNameUser->scores->first()->id => ['board_number' => $hideNameUser->scores->first()->board_number],
    ]);

    // Update leaderboards
    app(UpdatesPublicLeaderboards::class)->update(now());

    $leaderboard = PublicLeaderboard::getForever();

    $showNameEntry = $leaderboard->leaderboard->firstWhere('user_id', $showNameUser->id);
    $hideNameEntry = $leaderboard->leaderboard->firstWhere('user_id', $hideNameUser->id);

    expect((bool) $showNameEntry['show_name'])->toBeTrue();
    expect((bool) $hideNameEntry['show_name'])->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| Public Leaderboard Livewire Component Tests
|--------------------------------------------------------------------------
*/

it('can switch between time periods', function () {
    Livewire::test(\App\Http\Livewire\PublicLeaderboard::class)
        ->assertSet('period', 'forever')
        ->call('setPeriod', 'month')
        ->assertSet('period', 'month')
        ->call('setPeriod', 'week')
        ->assertSet('period', 'week');
});

it('can update leaderboard settings via inline form', function () {
    $user = User::factory()->create([
        'show_on_public_leaderboard' => false,
        'show_name_on_public_leaderboard' => false,
        'public_alias' => null,
    ]);

    $this->actingAs($user);

    Livewire::test(\App\Http\Livewire\PublicLeaderboard::class)
        ->set('publicAlias', 'MyAlias')
        ->set('showOnPublicLeaderboard', true)
        ->set('showNameOnPublicLeaderboard', true)
        ->call('saveSettings');

    $user->refresh();

    expect($user->public_alias)->toBe('MyAlias')
        ->and((bool) $user->show_on_public_leaderboard)->toBeTrue()
        ->and((bool) $user->show_name_on_public_leaderboard)->toBeTrue();
});

it('shows inline settings form for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/leaderboard')
        ->assertOk()
        ->assertSee('Your Leaderboard Settings');
});

it('does not show inline settings form for guests', function () {
    $this->get('/leaderboard')
        ->assertOk()
        ->assertDontSee('Your Leaderboard Settings');
});

/*
|--------------------------------------------------------------------------
| Public Leaderboard Settings Tests
|--------------------------------------------------------------------------
*/

it('can update public leaderboard settings', function () {
    $user = User::factory()->create([
        'show_on_public_leaderboard' => false,
        'show_name_on_public_leaderboard' => false,
    ]);

    // Test directly updating the user settings
    $user->update([
        'show_on_public_leaderboard' => true,
        'show_name_on_public_leaderboard' => true,
    ]);

    $user->refresh();

    expect((bool) $user->show_on_public_leaderboard)->toBeTrue();
    expect((bool) $user->show_name_on_public_leaderboard)->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| Registration With Leaderboard Settings Tests
|--------------------------------------------------------------------------
*/

it('can register with public leaderboard settings', function () {
    // Test that user creation supports the new fields
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test-leaderboard@example.com',
        'show_on_public_leaderboard' => true,
        'show_name_on_public_leaderboard' => true,
    ]);

    expect($user)->not->toBeNull();
    expect($user->show_on_public_leaderboard)->toBeTrue();
    expect($user->show_name_on_public_leaderboard)->toBeTrue();
});

it('defaults to not showing on public leaderboard', function () {
    // Test that defaults work correctly
    $user = User::create([
        'name' => 'Test User 2',
        'email' => 'test-leaderboard2@example.com',
    ]);

    expect($user)->not->toBeNull();
    // Database default is false - cast to bool for comparison
    expect((bool) $user->fresh()->show_on_public_leaderboard)->toBeFalse();
    expect((bool) $user->fresh()->show_name_on_public_leaderboard)->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| Update Public Leaderboards Command Tests
|--------------------------------------------------------------------------
*/

it('creates public leaderboard records when updating', function () {
    $user = User::factory()->create([
        'show_on_public_leaderboard' => true,
    ]);

    Score::withoutEvents(function () use ($user) {
        Score::factory()->create([
            'user_id' => $user->id,
            'recording_user_id' => $user->id,
        ]);
    });

    // Sync to daily scores
    $user->dailyScores()->syncWithoutDetaching([
        $user->scores->first()->id => ['board_number' => $user->scores->first()->board_number],
    ]);

    app(UpdatesPublicLeaderboards::class)->update(now());

    expect(PublicLeaderboard::for('forever')->exists())->toBeTrue();
    expect(PublicLeaderboard::for('month')->exists())->toBeTrue();
    expect(PublicLeaderboard::for('week')->exists())->toBeTrue();
});
