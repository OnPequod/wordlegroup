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
// Database backups moved OUT of the app on 2026-08-24: an infra-owned job
// (infra repo, provision-wordlegroup-backups.yml) now runs hourly + daily
// pg_dump → age-encrypt → B2, scheduled through Forge. It survives the app
// being broken and encrypts what the app uploaded in plaintext. The
// backup:database command remains as a manual utility only — do not
// re-schedule it without also changing the mirror and monitoring, which
// watch the infra pipeline's prefix and format.

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
