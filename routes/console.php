<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
*/

Schedule::command('model:prune')->daily();

// Database backups to S3
Schedule::command('backup:database daily')
    ->dailyAt('03:00')
    ->onOneServer()
    ->withoutOverlapping();

Schedule::command('backup:database weekly')
    ->weeklyOn(0, '03:30') // Sunday at 3:30 AM
    ->onOneServer()
    ->withoutOverlapping();

Schedule::command('backup:database monthly')
    ->monthlyOn(1, '04:00') // 1st of month at 4:00 AM
    ->onOneServer()
    ->withoutOverlapping();

// Update public leaderboards every 15 minutes
Schedule::command('leaderboards:update-public')
    ->everyFifteenMinutes()
    ->onOneServer()
    ->withoutOverlapping();

// Update daily summaries every 15 minutes
Schedule::command('summaries:update-daily')
    ->everyFifteenMinutes()
    ->onOneServer()
    ->withoutOverlapping();

// Sync puzzle answers daily
Schedule::command('puzzles:sync --days=3')
    ->dailyAt('06:30')
    ->onOneServer()
    ->withoutOverlapping();
