<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('model:prune')->daily();

        // Database backups to S3
        $schedule->command('backup:database daily --cleanup')
            ->dailyAt('03:00')
            ->onOneServer()
            ->withoutOverlapping();

        $schedule->command('backup:database weekly --cleanup')
            ->weeklyOn(0, '03:30') // Sunday at 3:30 AM
            ->onOneServer()
            ->withoutOverlapping();

        $schedule->command('backup:database monthly --cleanup')
            ->monthlyOn(1, '04:00') // 1st of month at 4:00 AM
            ->onOneServer()
            ->withoutOverlapping();

        // Update public leaderboards every 15 minutes
        $schedule->command('leaderboards:update-public')
            ->everyFifteenMinutes()
            ->onOneServer()
            ->withoutOverlapping();

        // Update daily summaries every 15 minutes
        $schedule->command('summaries:update-daily')
            ->everyFifteenMinutes()
            ->onOneServer()
            ->withoutOverlapping();

        // Sync puzzle answers daily
        $schedule->command('puzzles:sync --days=3')
            ->dailyAt('06:30')
            ->onOneServer()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
