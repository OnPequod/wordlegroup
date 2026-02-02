<?php

namespace App\Jobs;

use App\Concerns\UpdatesPublicLeaderboards;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdatePublicLeaderboardsJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public int $uniqueFor = 60;

    public function __construct()
    {
        $this->onQueue('default');
    }

    public function handle(UpdatesPublicLeaderboards $updater): void
    {
        $updater->update(now());
    }
}
