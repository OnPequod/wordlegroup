<?php

use App\Listeners\RecordScoreFromEmail;
use App\Listeners\SendGroupCreationEmail;
use App\Listeners\SendGroupMembershipCreationEmail;
use App\Listeners\SendUnverifiedGroupCreationEmail;
use App\Listeners\SendUserVerificationEmail;
use App\Mail\NudgeUser;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

it('uses queued listeners for email and score ingestion events', function () {
    expect(class_implements(SendUserVerificationEmail::class))->toContain(ShouldQueue::class)
        ->and(class_implements(SendUnverifiedGroupCreationEmail::class))->toContain(ShouldQueue::class)
        ->and(class_implements(SendGroupCreationEmail::class))->toContain(ShouldQueue::class)
        ->and(class_implements(SendGroupMembershipCreationEmail::class))->toContain(ShouldQueue::class)
        ->and(class_implements(RecordScoreFromEmail::class))->toContain(ShouldQueue::class);
});

it('queues nudge email instead of sending inline', function () {
    Mail::fake();

    $nudgedUser = User::factory()->create();
    $nudgedBy = User::factory()->create();

    $nudgedUser->nudgeUser($nudgedBy);

    Mail::assertQueued(NudgeUser::class, function (NudgeUser $mailable) use ($nudgedUser, $nudgedBy): bool {
        return $mailable->user->is($nudgedUser) && $mailable->nudgedByUser->is($nudgedBy);
    });
});
