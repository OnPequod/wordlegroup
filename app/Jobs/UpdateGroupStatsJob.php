<?php

namespace App\Jobs;

use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateGroupStatsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;
    public int $uniqueFor = 60;

    public function __construct(
        public Group $group
    ) {}

    public static function dispatchForUser(User $user): void
    {
        $dispatchedGroupIds = [];

        GroupMembership::query()
            ->where('user_id', $user->id)
            ->orderBy('id')
            ->chunkById(100, function ($memberships) use (&$dispatchedGroupIds): void {
                $groupIds = $memberships
                    ->pluck('group_id')
                    ->reject(function (int $groupId) use ($dispatchedGroupIds): bool {
                        return isset($dispatchedGroupIds[$groupId]);
                    })
                    ->values();

                foreach ($groupIds as $groupId) {
                    $dispatchedGroupIds[$groupId] = true;
                }

                Group::query()
                    ->whereKey($groupIds->all())
                    ->get()
                    ->each(function (Group $group): void {
                        app(Dispatcher::class)->dispatch(new self($group));
                    });
            }, column: 'id');
    }

    public function handle(): void
    {
        $this->group->updateStats();
    }

    public function uniqueId(): string
    {
        return 'group-stats-' . $this->group->id;
    }
}
