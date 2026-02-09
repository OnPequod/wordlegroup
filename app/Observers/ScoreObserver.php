<?php

namespace App\Observers;

use App\Jobs\SyncScoreDataJob;
use App\Models\Score;

class ScoreObserver
{
    public function saved(Score $score): void
    {
        SyncScoreDataJob::dispatch($score);
    }
}
