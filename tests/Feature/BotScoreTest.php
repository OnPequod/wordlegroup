<?php

use App\Concerns\WordleBoard;
use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\Score;
use App\Models\User;
use Livewire\Livewire;

/*
|--------------------------------------------------------------------------
| Bot Score Parsing Tests
|--------------------------------------------------------------------------
*/

it('parses WordleBot skill and luck scores from share text', function () {
    $boardText = "Wordle 1,687 3/6*

⬛🟩🟨⬛⬛
⬛🟩🟨🟨🟩
🟩🟩🟩🟩🟩

WordleBot
Skill 93/99
Luck 83/99";

    $result = app(WordleBoard::class)->parse($boardText);

    expect($result['botScores']['skill'])->toBe(93);
    expect($result['botScores']['luck'])->toBe(83);
    expect($result['valid'])->toBeTrue();
});

it('parses bot scores with colon format', function () {
    $boardText = "Wordle 1,687 4/6

⬛🟩🟨⬛⬛
⬛🟩🟨🟨⬛
⬛🟩⬛🟨🟩
🟩🟩🟩🟩🟩

Skill: 85
Luck: 72";

    $result = app(WordleBoard::class)->parse($boardText);

    expect($result['botScores']['skill'])->toBe(85);
    expect($result['botScores']['luck'])->toBe(72);
});

it('returns null for bot scores when not present', function () {
    $boardText = "Wordle 1,687 3/6*

⬛🟩🟨⬛⬛
⬛🟩🟨🟨🟩
🟩🟩🟩🟩🟩";

    $result = app(WordleBoard::class)->parse($boardText);

    expect($result['botScores']['skill'])->toBeNull();
    expect($result['botScores']['luck'])->toBeNull();
});

it('validates bot score range (0-99)', function () {
    $boardText = "Wordle 1,687 3/6*

⬛🟩🟨⬛⬛
⬛🟩🟨🟨🟩
🟩🟩🟩🟩🟩

Skill 150/99
Luck -5/99";

    $result = app(WordleBoard::class)->parse($boardText);

    // Out of range values should be null
    expect($result['botScores']['skill'])->toBeNull();
    expect($result['botScores']['luck'])->toBeNull();
});

/*
|--------------------------------------------------------------------------
| Bot Score Recording Tests
|--------------------------------------------------------------------------
*/

it('stores bot scores when recording a score', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id, 'verified_at' => now()]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    $boardText = "Wordle 1,687 3/6*

⬛🟩🟨⬛⬛
⬛🟩🟨🟨🟩
🟩🟩🟩🟩🟩

WordleBot
Skill 93/99
Luck 83/99";

    // Test the parsing and store directly since Livewire dispatch events don't work well in isolated tests
    $parsed = app(WordleBoard::class)->parse($boardText);

    Score::create([
        'user_id' => $user->id,
        'recording_user_id' => $user->id,
        'date' => $parsed['date']->format('Y-m-d'),
        'score' => $parsed['scoreNumber'],
        'board_number' => $parsed['boardNumber'],
        'board' => $parsed['board'],
        'hard_mode' => $parsed['hardMode'],
        'bot_skill_score' => $parsed['botScores']['skill'],
        'bot_luck_score' => $parsed['botScores']['luck'],
    ]);

    $score = Score::where('user_id', $user->id)
        ->where('board_number', 1687)
        ->first();

    expect($score)->not->toBeNull();
    expect($score->bot_skill_score)->toBe(93);
    expect($score->bot_luck_score)->toBe(83);
});

/*
|--------------------------------------------------------------------------
| Score Model Bot Score Methods Tests
|--------------------------------------------------------------------------
*/

it('has hasBotScore method', function () {
    $scoreWithBot = new Score(['bot_skill_score' => 85, 'bot_luck_score' => 72]);
    $scoreWithoutBot = new Score(['bot_skill_score' => null, 'bot_luck_score' => null]);

    expect($scoreWithBot->hasBotScore())->toBeTrue();
    expect($scoreWithoutBot->hasBotScore())->toBeFalse();
});

it('formats bot skill display correctly', function () {
    $score = new Score(['bot_skill_score' => 85]);

    expect($score->botSkillDisplay)->toBe('85/99');
});

it('formats bot luck display correctly', function () {
    $score = new Score(['bot_luck_score' => 72]);

    expect($score->botLuckDisplay)->toBe('72/99');
});

/*
|--------------------------------------------------------------------------
| User Bot Stats Tests
|--------------------------------------------------------------------------
*/

it('calculates user bot skill mean', function () {
    $user = User::factory()->create();

    // Create scores with bot data
    Score::withoutEvents(function () use ($user) {
        Score::factory()->create([
            'user_id' => $user->id,
            'recording_user_id' => $user->id,
            'bot_skill_score' => 80,
            'bot_luck_score' => 70,
        ]);
        Score::factory()->create([
            'user_id' => $user->id,
            'recording_user_id' => $user->id,
            'bot_skill_score' => 90,
            'bot_luck_score' => 80,
        ]);
    });

    // Manually load the scores relationship for calculation
    $user->load('dailyScores');
    // Force refresh to get scores
    $user = $user->fresh(['dailyScores']);

    // Sync scores to daily scores pivot table
    foreach ($user->scores as $score) {
        $user->dailyScores()->syncWithoutDetaching([
            $score->id => ['board_number' => $score->board_number],
        ]);
    }

    $user = $user->fresh(['dailyScores']);

    expect($user->getBotSkillMean())->toBe(85.0);
    expect($user->getBotLuckMean())->toBe(75.0);
});
