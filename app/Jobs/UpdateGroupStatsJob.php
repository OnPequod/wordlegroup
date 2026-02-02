<?php

namespace App\Jobs;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateGroupStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        public Group $group
    ) {}

    public function handle(): void
    {
        $this->group->updateStats();
    }

    public function uniqueId(): string
    {
        return 'group-stats-' . $this->group->id;
    }
}
