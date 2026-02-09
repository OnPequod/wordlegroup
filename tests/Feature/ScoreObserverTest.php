<?php

use App\Jobs\SyncScoreDataJob;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

it('dispatches SyncScoreDataJob when a score is created', function () {
    Queue::fake();

    $user = User::factory()->create();

    $score = Score::factory()->create([
        'user_id' => $user->id,
        'recording_user_id' => $user->id,
    ]);

    Queue::assertPushed(SyncScoreDataJob::class, function ($job) use ($score) {
        return $job->score->id === $score->id;
    });
});

it('dispatches SyncScoreDataJob only once on create', function () {
    Queue::fake();

    $user = User::factory()->create();

    Score::factory()->create([
        'user_id' => $user->id,
        'recording_user_id' => $user->id,
    ]);

    // saved fires once on create - should only dispatch once
    Queue::assertPushed(SyncScoreDataJob::class, 1);
});

it('dispatches SyncScoreDataJob only once on update', function () {
    Queue::fake();

    $user = User::factory()->create();

    $score = Score::withoutEvents(function () use ($user) {
        return Score::factory()->create([
            'user_id' => $user->id,
            'recording_user_id' => $user->id,
        ]);
    });

    Queue::assertPushed(SyncScoreDataJob::class, 0);

    $score->update(['score' => 3]);

    // saved fires once on update - should only dispatch once
    Queue::assertPushed(SyncScoreDataJob::class, 1);
});
