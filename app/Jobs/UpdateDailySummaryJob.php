<?php

namespace App\Jobs;

use App\Concerns\UpdatesDailySummaries;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateDailySummaryJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uniqueFor = 60;

    public function __construct(public int $boardNumber)
    {
    }

    public function handle(): void
    {
        app(UpdatesDailySummaries::class)->updateForBoardNumber($this->boardNumber);
    }

    public function uniqueId(): string
    {
        return 'daily-summary-' . $this->boardNumber;
    }
}
