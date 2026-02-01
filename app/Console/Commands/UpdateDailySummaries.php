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

        if ($this->option('board')) {
            $boardNumber = (int) $this->option('board');
            $this->info("Updating summary for board #{$boardNumber}...");
            $updater->updateForBoardNumber($boardNumber);
            $this->info("Summary for board #{$boardNumber} updated successfully.");
            return Command::SUCCESS;
        }

        if ($this->option('rebuild')) {
            $this->info('Rebuilding all daily summaries...');
            $updater->rebuildAll();
            $this->info('All daily summaries rebuilt successfully.');
            return Command::SUCCESS;
        }

        $this->info('Updating recent daily summaries...');
        $updater->updateRecent();
        $this->info('Recent daily summaries updated successfully.');

        return Command::SUCCESS;
    }
}
