<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendGroupMembershipCreationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle($event): void
    {
        Mail::to($event->groupMembership->user->email)
            ->send(new \App\Mail\GroupMembershipCreated($event->groupMembership));
    }
}
