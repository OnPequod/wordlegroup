<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendUserVerificationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event): void
    {
        $event->user->sendEmailVerificationNotification();
    }
}
