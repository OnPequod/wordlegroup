<?php

use App\Concerns\RecordsMailScore;
use App\Concerns\WordleBoard;
use App\Http\Livewire\Account\Login;
use App\Models\GroupMembershipInvitation;
use App\Models\MailScoreMessage;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

it('stores user and invitation emails lowercased', function () {
    $user = User::factory()->create(['email' => '  Ketzscher@Hotmail.com ']);
    $invitation = GroupMembershipInvitation::factory()->create(['email' => 'Invitee@Example.com']);

    expect($user->fresh()->email)->toBe('ketzscher@hotmail.com')
        ->and($invitation->fresh()->email)->toBe('invitee@example.com');
});

it('logs in regardless of email case', function () {
    Mail::fake();

    User::factory()->create(['email' => 'player@example.com']);

    Livewire::test(Login::class)
        ->set('email', 'Player@Example.COM')
        ->call('send')
        ->assertHasNoErrors()
        ->assertSet('codeSent', true);
});

it('records a mailed score when the sender address case differs', function () {
    Queue::fake();

    $user = User::factory()->create(['email' => 'ketzscher@hotmail.com']);
    $boardNumber = number_format(app(WordleBoard::class)->activeBoardNumber);

    $raw = "From: Ketzscher@Hotmail.com\r\n"
        . "To: scores@wordlegroup.com\r\n"
        . "Subject: Wordle\r\n"
        . "Content-Type: text/plain; charset=UTF-8\r\n\r\n"
        . "Wordle {$boardNumber} 4/6\n\n⬜⬜⬜⬜⬜\n🟨⬜⬜⬜⬜\n🟨🟩⬜⬜⬜\n🟩🟩🟩🟩🟩\n";

    $message = MailScoreMessage::create(['message' => ['content' => base64_encode($raw)]]);

    app(RecordsMailScore::class)->record($message);

    expect(Score::query()->where('user_id', $user->id)->where('score', 4)->count())->toBe(1)
        ->and($message->fresh()->processed_at)->not->toBeNull();
})->skip(! extension_loaded('mailparse'), 'requires the mailparse extension');
