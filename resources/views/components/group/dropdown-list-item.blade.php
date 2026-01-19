<a href="{{ route('group.home', $groupMembership->group) }}">
    <div class="float-right">
        @if($groupMembership->group->isAdmin($user))
            <x-group.admin-badge class="ml-2" text-size="text-xs" />
        @endif
    </div>
    <div class="font-bold text-green-700">{{ $groupMembership->group->name }}</div>
    <div class="mt-2">
        <ul class="text-sm text-zinc-600">
            @if(!empty($groupMembership->group->leaderboard) && isset($groupMembership?->group?->leaderboard?->first()['name']))
                <li class="mt-1 first:mt-0">
                    <span class="font-semibold text-zinc-700">Leader:</span>
                    {{ $groupMembership->group->leaderboard->first()['name']  }}, {{ number_format($groupMembership->group->leaderboard->first()['stats']['mean'], 2) }}
                </li>
                <li class="mt-1 first:mt-0">
                    <span class="font-semibold text-zinc-700">Avg. Score:</span>
                    {{ number_format($groupMembership->group->score_mean, 2) }}
                </li>
                @if(!empty($groupMembership->group->leaderboard->firstWhere('user_id', $user->id)))
                    <li class="mt-1 first:mt-0">
                        <span class="font-semibold text-zinc-700">My Place:</span>
                        @if(!empty($groupMembership->group->activeLeaderboards->firstWhere('for', 'forever')))
                        {{ $groupMembership->group->activeLeaderboards->firstWhere('for', 'forever')->leaderboard->firstWhere('user_id', $user->id)['place'] }}/{{ $groupMembership->group->activeLeaderboards->firstWhere('for', 'forever')->leaderboard->pluck('place')->max() }} Overall,
                        @endif
                        @if($groupMembership->group->activeLeaderboards->firstWhere('for', 'month') && $groupMembership->group->activeLeaderboards->firstWhere('for', 'month')->leaderboard->firstWhere('user_id', $user->id))
                            {{ $groupMembership->group->activeLeaderboards->firstWhere('for', 'month')->leaderboard->firstWhere('user_id', $user->id)['place'] }}/{{ $groupMembership->group->activeLeaderboards->firstWhere('for', 'month')->leaderboard->pluck('place')->max() }} This Month
                        @else
                            none this month.
                        @endif
                    </li>
                @endif
            @endif
        </ul>
    </div>
</a>
