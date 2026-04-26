<?php

use App\Concerns\WordleBoard;
use App\Http\Livewire\Score\RecordForm;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

it('records a score from pasted board text', function () {
    Queue::fake();

    $user = User::factory()->create();
    $boardNumber = app(WordleBoard::class)->activeBoardNumber;
    $formattedBoardNumber = number_format($boardNumber);

    $board = "Wordle {$formattedBoardNumber} 4/6\n\n⬜⬜⬜⬜⬜\n🟨⬜⬜⬜⬜\n🟨🟩⬜⬜⬜\n🟩🟩🟩🟩🟩";

    Livewire::actingAs($user)
        ->test(RecordForm::class, ['user' => $user])
        ->set('board', $board)
        ->call('recordScoreFromBoard')
        ->assertRedirect(route('account.home'));

    expect(Score::query()
        ->where('user_id', $user->id)
        ->where('recording_user_id', $user->id)
        ->where('score', 4)
        ->count())->toBe(1);
});

it('records a score from manual entry once', function () {
    Queue::fake();

    $user = User::factory()->create();
    $date = app(WordleBoard::class)->activeBoardStartTime->format('Y-m-d');
    $boardNumber = app(WordleBoard::class)->getBoardNumberFromDate($date);

    Livewire::actingAs($user)
        ->test(RecordForm::class, ['user' => $user])
        ->set('date', $date)
        ->set('boardNumber', $boardNumber)
        ->set('score', 3)
        ->set('hardMode', true)
        ->call('recordScoreManually')
        ->assertRedirect(route('account.home'));

    expect(Score::query()
        ->where('user_id', $user->id)
        ->where('recording_user_id', $user->id)
        ->where('board_number', $boardNumber)
        ->where('score', 3)
        ->count())->toBe(1);
});
