<?php

namespace App\Jobs;

use App\Concerns\UpdatesDailySummaries;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateDailySummariesJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    public int $uniqueFor = 60;

    public function __construct(public ?int $boardNumber = null)
    {
        $this->onQueue('default');
    }

    public function uniqueId(): string
    {
        return $this->boardNumber ? "board-{$this->boardNumber}" : 'recent';
    }

    public function handle(UpdatesDailySummaries $updater): void
    {
        if ($this->boardNumber !== null) {
            $updater->updateForBoardNumber($this->boardNumber);
            return;
        }

        $updater->updateRecent();
    }
}
