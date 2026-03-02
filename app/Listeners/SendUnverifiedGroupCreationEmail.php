<?php

namespace App\Listeners;

use App\Mail\UnverifiedGroupCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendUnverifiedGroupCreationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event): void
    {
        Mail::to($event->group->admin->email)
            ->send(new UnverifiedGroupCreated($event->group));
    }
}
