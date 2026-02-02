<?php

namespace App\Console\Commands;

use App\Concerns\UpdatesPublicLeaderboards as UpdatesPublicLeaderboardsConcern;
use Illuminate\Console\Command;

class UpdatePublicLeaderboards extends Command
{
    protected $signature = 'leaderboards:update-public';

    protected $description = 'Update public leaderboards for all time periods';

    public function handle(): int
    {
        $this->info('Updating public leaderboards...');

        app(UpdatesPublicLeaderboardsConcern::class)->update(now());

        $this->info('Public leaderboards updated successfully.');

        return Command::SUCCESS;
    }
}
