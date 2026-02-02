<?php

namespace App\Console\Commands;

use App\Concerns\UpdatesDailySummaries as UpdatesDailySummariesConcern;
use Illuminate\Console\Command;

class UpdateDailySummaries extends Command
{
    protected $signature = 'summaries:update-daily
                            {--rebuild : Rebuild all summaries from scratch}
                            {--board= : Update a specific board number}';

    protected $description = 'Update daily puzzle summaries';

    public function handle(): int
    {
        $updater = app(UpdatesDailySummariesConcern::class);

        if ($board = $this->option('board')) {
            $this->info("Updating summary for board #{$board}...");
            $updater->updateForBoardNumber((int) $board);
            $this->info('Done.');
            return self::SUCCESS;
        }

        if ($this->option('rebuild')) {
            $this->info('Rebuilding all daily summaries...');
            $updater->rebuildAll();
            $this->info('All summaries rebuilt.');
            return self::SUCCESS;
        }

        $this->info('Updating recent summaries (today + yesterday)...');
        $updater->updateRecent();
        $this->info('Done.');

        return self::SUCCESS;
    }
}
