<?php

namespace App\Jobs;

use App\Models\Score;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncScoreDataJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(public Score $score) {}

    public function handle(): void
    {
        $this->score->syncToDailyScores();
        $this->score->syncToGroupMemberships();
        $this->score->user->updateStats();

        $this->score->user->memberships->each(function ($membership) {
            UpdateGroupStatsJob::dispatch($membership->group);
        });
    }
}
