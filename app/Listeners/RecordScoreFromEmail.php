<?php

namespace App\Listeners;

use App\Concerns\RecordsMailScore;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RecordScoreFromEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event): void
    {
        app(RecordsMailScore::class)->record($event->message);
    }
}
