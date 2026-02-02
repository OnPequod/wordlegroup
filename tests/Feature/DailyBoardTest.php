<?php

use App\Concerns\UpdatesDailySummaries;
use App\Concerns\WordleDate;
use App\Http\Livewire\DailyBoard;
use App\Models\DailySummary;
use App\Models\Score;
use App\Models\User;
use Livewire\Livewire;

/*
|--------------------------------------------------------------------------
| Daily Board Route Tests
|--------------------------------------------------------------------------
*/

it('returns a successful response for the daily board page', function () {
    $this->get('/board')->assertOk();
});

it('returns a successful response for a specific board number', function () {
    $boardNumber = app(WordleDate::class)->activeBoardNumber - 10;
    $this->get("/board/{$boardNumber}")->assertOk();
});

it('shows the board number in the page title', function () {
    $boardNumber = app(WordleDate::class)->activeBoardNumber;

    $this->get('/board')
        ->assertOk()
        ->assertSee("Wordle #{$boardNumber}");
});

it('shows Today badge for current puzzle', function () {
    $this->get('/board')
        ->assertOk()
        ->assertSee('Today');
});

it('does not show Today badge for past puzzles', function () {
    $boardNumber = app(WordleDate::class)->activeBoardNumber - 5;

    // The Today badge has specific classes that distinguish it from the "Today" navigation button
    $this->get("/board/{$boardNumber}")
        ->assertOk()
        ->assertDontSee('bg-green-100'); // The Today badge styling
});

it('returns 404 for invalid board numbers', function () {
    $this->get('/board/999999999')->assertNotFound();
});

it('returns 404 for negative board numbers', function () {
    $this->get('/board/-1')->assertNotFound();
});

/*
|--------------------------------------------------------------------------
| Daily Board Navigation Tests
|--------------------------------------------------------------------------
*/

it('can navigate to previous board', function () {
    $boardNumber = app(WordleDate::class)->activeBoardNumber;

    Livewire::test(DailyBoard::class, ['boardNumber' => $boardNumber])
        ->call('goToPrevious')
        ->assertRedirect(route('board', $boardNumber - 1));
});

it('can navigate to next board', function () {
    $boardNumber = app(WordleDate::class)->activeBoardNumber - 5;

    Livewire::test(DailyBoard::class, ['boardNumber' => $boardNumber])
        ->call('goToNext')
        ->assertRedirect(route('board', $boardNumber + 1));
});

it('can navigate to today', function () {
    $boardNumber = app(WordleDate::class)->activeBoardNumber - 5;

    Livewire::test(DailyBoard::class, ['boardNumber' => $boardNumber])
        ->call('goToToday')
        ->assertRedirect(route('board'));
});

it('cannot navigate before first board', function () {
    Livewire::test(DailyBoard::class, ['boardNumber' => 0])
        ->assertSet('hasPrevious', false);
});

it('cannot navigate after current board', function () {
    $boardNumber = app(WordleDate::class)->activeBoardNumber;

    Livewire::test(DailyBoard::class, ['boardNumber' => $boardNumber])
        ->assertSet('hasNext', false);
});

/*
|--------------------------------------------------------------------------
| Daily Summary Aggregation Tests
|--------------------------------------------------------------------------
*/

it('creates daily summary when updating for a board number', function () {
    $user = User::factory()->create([
        'show_on_public_leaderboard' => true,
        'show_name_on_public_leaderboard' => true,
        'name' => 'Test User',
    ]);

    $boardNumber = app(WordleDate::class)->activeBoardNumber - 1;

    Score::withoutEvents(function () use ($user, $boardNumber) {
        Score::factory()->create([
            'user_id' => $user->id,
            'recording_user_id' => $user->id,
            'board_number' => $boardNumber,
            'score' => 4,
        ]);
    });

    app(UpdatesDailySummaries::class)->updateForBoardNumber($boardNumber);

    $summary = DailySummary::getByBoardNumber($boardNumber);

    expect($summary)->not->toBeNull();
    expect($summary->participant_count)->toBe(1);
    expect($summary->score_mean)->toBe(4.0);
});

it('only includes opted-in users in daily summary', function () {
    $optedInUser = User::factory()->create([
        'show_on_public_leaderboard' => true,
    ]);
    $optedOutUser = User::factory()->create([
        'show_on_public_leaderboard' => false,
    ]);

    $boardNumber = app(WordleDate::class)->activeBoardNumber - 2;

    Score::withoutEvents(function () use ($optedInUser, $optedOutUser, $boardNumber) {
        Score::factory()->create([
            'user_id' => $optedInUser->id,
            'recording_user_id' => $optedInUser->id,
            'board_number' => $boardNumber,
            'score' => 3,
        ]);
        Score::factory()->create([
            'user_id' => $optedOutUser->id,
            'recording_user_id' => $optedOutUser->id,
            'board_number' => $boardNumber,
            'score' => 5,
        ]);
    });

    app(UpdatesDailySummaries::class)->updateForBoardNumber($boardNumber);

    $summary = DailySummary::getByBoardNumber($boardNumber);

    expect($summary->participant_count)->toBe(1);
    expect($summary->score_mean)->toBe(3.0);
});

it('calculates score distribution correctly', function () {
    $users = User::factory()->count(5)->create([
        'show_on_public_leaderboard' => true,
    ]);

    $boardNumber = app(WordleDate::class)->activeBoardNumber - 3;
    $scores = [3, 3, 4, 4, 5];

    Score::withoutEvents(function () use ($users, $boardNumber, $scores) {
        foreach ($users as $index => $user) {
            Score::factory()->create([
                'user_id' => $user->id,
                'recording_user_id' => $user->id,
                'board_number' => $boardNumber,
                'score' => $scores[$index],
            ]);
        }
    });

    app(UpdatesDailySummaries::class)->updateForBoardNumber($boardNumber);

    $summary = DailySummary::getByBoardNumber($boardNumber);

    expect($summary->score_distribution[3])->toBe(2);
    expect($summary->score_distribution[4])->toBe(2);
    expect($summary->score_distribution[5])->toBe(1);
});

it('includes board data in summary for opted-in users', function () {
    $user = User::factory()->create([
        'show_on_public_leaderboard' => true,
        'show_name_on_public_leaderboard' => true,
        'name' => 'Board Test User',
    ]);

    $boardNumber = app(WordleDate::class)->activeBoardNumber - 4;

    Score::withoutEvents(function () use ($user, $boardNumber) {
        Score::factory()->create([
            'user_id' => $user->id,
            'recording_user_id' => $user->id,
            'board_number' => $boardNumber,
            'score' => 4,
            'board' => "🟩🟩🟩🟩🟩",
        ]);
    });

    app(UpdatesDailySummaries::class)->updateForBoardNumber($boardNumber);

    $summary = DailySummary::getByBoardNumber($boardNumber);

    expect($summary->boards)->toHaveCount(1);
    expect($summary->boards->first()['name'])->toBe('Board Test User');
});

it('hides name in board data when user disables show_name_on_public_leaderboard', function () {
    $user = User::factory()->create([
        'show_on_public_leaderboard' => true,
        'show_name_on_public_leaderboard' => false,
        'name' => 'Hidden Name User',
    ]);

    $boardNumber = app(WordleDate::class)->activeBoardNumber - 5;

    Score::withoutEvents(function () use ($user, $boardNumber) {
        Score::factory()->create([
            'user_id' => $user->id,
            'recording_user_id' => $user->id,
            'board_number' => $boardNumber,
            'score' => 4,
        ]);
    });

    app(UpdatesDailySummaries::class)->updateForBoardNumber($boardNumber);

    $summary = DailySummary::getByBoardNumber($boardNumber);

    expect($summary->boards->first()['name'])->toBeNull();
});

/*
|--------------------------------------------------------------------------
| Daily Board Spoiler Protection Tests
|--------------------------------------------------------------------------
*/

it('shows boards for past puzzles to guests', function () {
    $boardNumber = app(WordleDate::class)->activeBoardNumber - 1;

    DailySummary::factory()->forBoardNumber($boardNumber)->withBoards([
        ['user_id' => 1, 'name' => 'Test User', 'score' => 4, 'board' => '🟩🟩🟩🟩🟩', 'hard_mode' => false, 'bot_skill' => null, 'bot_luck' => null],
    ])->create();

    Livewire::test(DailyBoard::class, ['boardNumber' => $boardNumber])
        ->assertSet('canViewBoards', true);
});

it('hides boards for current puzzle from guests', function () {
    $boardNumber = app(WordleDate::class)->activeBoardNumber;

    DailySummary::factory()->forBoardNumber($boardNumber)->withBoards([
        ['user_id' => 1, 'name' => 'Test User', 'score' => 4, 'board' => '🟩🟩🟩🟩🟩', 'hard_mode' => false, 'bot_skill' => null, 'bot_luck' => null],
    ])->create();

    Livewire::test(DailyBoard::class, ['boardNumber' => $boardNumber])
        ->assertSet('canViewBoards', false);
});

it('shows boards for current puzzle to users who have recorded their score', function () {
    $user = User::factory()->create();
    $boardNumber = app(WordleDate::class)->activeBoardNumber;

    Score::withoutEvents(function () use ($user, $boardNumber) {
        Score::factory()->create([
            'user_id' => $user->id,
            'recording_user_id' => $user->id,
            'board_number' => $boardNumber,
        ]);
    });

    DailySummary::factory()->forBoardNumber($boardNumber)->withBoards([
        ['user_id' => 1, 'name' => 'Test User', 'score' => 4, 'board' => '🟩🟩🟩🟩🟩', 'hard_mode' => false, 'bot_skill' => null, 'bot_luck' => null],
    ])->create();

    $this->actingAs($user);

    Livewire::test(DailyBoard::class, ['boardNumber' => $boardNumber])
        ->assertSet('canViewBoards', true);
});

it('hides boards for current puzzle from users who have not recorded their score', function () {
    $user = User::factory()->create();
    $boardNumber = app(WordleDate::class)->activeBoardNumber;

    DailySummary::factory()->forBoardNumber($boardNumber)->withBoards([
        ['user_id' => 1, 'name' => 'Test User', 'score' => 4, 'board' => '🟩🟩🟩🟩🟩', 'hard_mode' => false, 'bot_skill' => null, 'bot_luck' => null],
    ])->create();

    $this->actingAs($user);

    Livewire::test(DailyBoard::class, ['boardNumber' => $boardNumber])
        ->assertSet('canViewBoards', false);
});

/*
|--------------------------------------------------------------------------
| Daily Summary Model Tests
|--------------------------------------------------------------------------
*/

it('has correct difficulty label for easy puzzles', function () {
    $summary = DailySummary::factory()->easy()->create();

    expect($summary->difficultyLabel)->toBe('Easy');
    expect($summary->difficultyColor)->toBe('text-green-600');
});

it('has correct difficulty label for hard puzzles', function () {
    $summary = DailySummary::factory()->hard()->create();

    expect($summary->difficultyLabel)->toBe('Hard');
    expect($summary->difficultyColor)->toBe('text-red-600');
});

it('has correct difficulty label for average puzzles', function () {
    $summary = DailySummary::factory()->create(['difficulty_delta' => 0.2]);

    expect($summary->difficultyLabel)->toBe('Average');
    expect($summary->difficultyColor)->toBe('text-zinc-600');
});

it('formats score distribution correctly', function () {
    $summary = DailySummary::factory()->create([
        'score_distribution' => collect([1 => 2, 2 => 5, 3 => 10, 4 => 20, 5 => 10, 6 => 3, 'X' => 0]),
    ]);

    $formatted = $summary->formattedDistribution;

    expect($formatted)->toHaveCount(7);
    expect($formatted[4]['count'])->toBe(20);
    expect($formatted[4]['percentage'])->toBeGreaterThan(0);
});
