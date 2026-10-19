<?php

use App\Http\Livewire\Group\PendingInvitations;
use App\Mail\GroupInvitationReminder;
use App\Models\Group;
use App\Models\GroupMembershipInvitation;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

it('sends a reminder and still renders the invitation afterwards', function () {
    Mail::fake();

    $group = Group::factory()->create();
    $invitation = GroupMembershipInvitation::factory()->create(['group_id' => $group->id]);

    Livewire::actingAs($group->admin)
        ->test(PendingInvitations::class, ['group' => $group])
        ->call('remind', $invitation->id)
        ->assertRedirect(route('group.home', $group));

    Mail::assertSent(GroupInvitationReminder::class);

    expect($invitation->fresh()->reminded_at)->toBeInstanceOf(\Carbon\CarbonInterface::class);

    Livewire::actingAs($group->admin)
        ->test(PendingInvitations::class, ['group' => $group->fresh()])
        ->assertSee('Reminded');
});
