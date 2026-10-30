<?php

use App\Concerns\WordleDate;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->travelTo(now()->parse('2026-10-30 12:00:00 UTC'));
});

function misparsedScore(array $attributes = []): Score
{
    return Score::factory()->create(array_merge([
        'board_number' => 959,
        'date' => app(WordleDate::class)->getDateFromBoardNumber(959)->format('Y-m-d'),
        'created_at' => now()->subHour(),
    ], $attributes));
}

it('moves misparsed scores to the board 1,000 higher', function () {
    $score = misparsedScore();

    expect(DB::table('user_score')->where('score_id', $score->id)->value('board_number'))->toBe(959);

    $this->artisan('scores:fix-misparsed-board-numbers')->assertSuccessful();

    $score->refresh();

    expect($score->board_number)->toBe(1959);
    expect($score->date->format('Y-m-d'))
        ->toBe(app(WordleDate::class)->getDateFromBoardNumber(1959)->format('Y-m-d'));
    expect(DB::table('user_score')->where('score_id', $score->id)->pluck('board_number')->all())->toBe([1959]);
    expect($score->user->fresh()->daily_scores_recorded)->toBe(1);
});

it('keeps a real score on the old board as the daily score', function () {
    $user = User::factory()->create();
    $realOldScore = misparsedScore(['user_id' => $user->id, 'recording_user_id' => $user->id, 'created_at' => now()->subYears(2)]);
    $misparsed = misparsedScore(['user_id' => $user->id, 'recording_user_id' => $user->id]);

    $this->artisan('scores:fix-misparsed-board-numbers')->assertSuccessful();

    expect($realOldScore->fresh()->board_number)->toBe(959);
    expect($misparsed->fresh()->board_number)->toBe(1959);
    expect(DB::table('user_score')->where('user_id', $user->id)->orderBy('board_number')->pluck('score_id', 'board_number')->all())
        ->toBe([959 => $realOldScore->id, 1959 => $misparsed->id]);
});

it('leaves backfilled and manually entered scores alone', function () {
    $backfilled = misparsedScore(['created_at' => now()->subDays(10)]);
    $manual = misparsedScore(['board' => null]);

    $this->artisan('scores:fix-misparsed-board-numbers')->assertSuccessful();

    expect($backfilled->fresh()->board_number)->toBe(959);
    expect($manual->fresh()->board_number)->toBe(959);
});

it('changes nothing on a dry run', function () {
    $score = misparsedScore();

    $this->artisan('scores:fix-misparsed-board-numbers', ['--dry-run' => true])
        ->expectsOutputToContain('1 scores would be moved.')
        ->assertSuccessful();

    expect($score->fresh()->board_number)->toBe(959);
});
