<?php

use App\Jobs\UpdateGroupStatsJob;
use App\Jobs\UpdateUserStatsJob;
use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

it('queues group stats refresh when a user display name changes', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    Queue::fake();

    $user->update(['name' => 'Updated Name']);

    Queue::assertPushed(UpdateGroupStatsJob::class, function (UpdateGroupStatsJob $job) use ($group): bool {
        return $job->group->is($group);
    });
});

it('does not queue group stats refresh for unrelated user field updates', function () {
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $user->id]);
    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    Queue::fake();

    $user->update(['dismissed_email_notification' => true]);

    Queue::assertNotPushed(UpdateGroupStatsJob::class);
});

it('queues user and group stats refresh when membership is created', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();
    $group = Group::factory()->create(['admin_user_id' => $admin->id]);

    Queue::fake();

    GroupMembership::factory()->create(['user_id' => $user->id, 'group_id' => $group->id]);

    Queue::assertPushed(UpdateUserStatsJob::class, function (UpdateUserStatsJob $job) use ($user): bool {
        return $job->user->is($user);
    });

    Queue::assertPushed(UpdateGroupStatsJob::class, function (UpdateGroupStatsJob $job) use ($group): bool {
        return $job->group->is($group);
    });
});
