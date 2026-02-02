<?php

namespace App\Observers;

use App\Jobs\UpdateGroupStatsJob;
use App\Models\Score;

class ScoreObserver
{
    public function updated(Score $score)
    {
        $this->runEvents($score);
    }

    public function saved(Score $score)
    {
        $this->runEvents($score);
    }

    public function runEvents(Score $score)
    {
        $score->syncToDailyScores();
        $score->syncToGroupMemberships();
        $score->user->updateStats();

        // Queue group stats updates instead of running synchronously
        $score->user->memberships->each(function ($membership) {
            UpdateGroupStatsJob::dispatch($membership->group);
        });
    }
}
