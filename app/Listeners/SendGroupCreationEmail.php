<?php

namespace App\Listeners;

use App\Mail\GroupCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendGroupCreationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event): void
    {
        Mail::to($event->group->admin->email)
            ->send(new GroupCreated($event->group));
    }
}
