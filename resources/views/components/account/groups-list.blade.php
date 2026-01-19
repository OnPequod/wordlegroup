<div>
    <ul role="list" class="flex flex-col gap-4">
        @foreach($groups as $group)
            <li class="flex rounded-md shadow-sm">
                <a
                    href="{{ route('group.home', $group) }}"
                    class="flex flex-grow overflow-hidden"
                    x-data="{hover: false}"
                    @mouseover="hover = true"
                    @mouseout="hover = false"
                >
                    <div
                        class="flex-shrink-0 flex items-center bg-wordle-yellow justify-center w-16 text-white text-2xl font-bold rounded-l-md"
                        :class="{ 'bg-green-700': hover, 'bg-wordle-yellow':  !hover}"
                    >
                        {{ Str::substr($group->name, 0, 1)  }}
                    </div>
                    <div
                        class="flex-grow flex items-center justify-between border-t border-r border-b border-gray-200 bg-white rounded-r-md truncate"
                    >
                      <div class="flex-1 px-4 py-3 truncate">
                          @if($userIsAuthenticatedUser && $group->isAdmin($user))
                              <div class="float-right ml-3">
                                  <x-group.admin-badge text-size="text-xs"/>
                              </div>
                          @endif
                            <span
                                class="text-gray-900 font-bold"
                            >{{ $group->name }}</span>
                            <ul class="mt-0.5 text-sm">
                                @if(!empty($group->leaderboard) && isset($group->leaderboard?->first()['name']))
                                    @if($group['leader'])
                                    <li>
                                        <span class="font-semibold">Leader:</span>

                                        {{ $group['leader']['public_name'] }}, {{ number_format($group->leaderboard->first()['stats']['mean'], 2) }}
                                    </li>
                                    @endif
                                    <li>
                                        <span class="font-semibold">Avg. Score:</span>
                                        {{ number_format($group->score_mean, 2) }}
                                    </li>
                                    @if($group?->leaderboards?->firstWhere('for', 'forever')?->leaderboard?->firstWhere('user_id', $user->id))
                                    <li>
                                        <span class="font-semibold">Place:</span>
                                        {{ $group->leaderboards->firstWhere('for', 'forever')->leaderboard->firstWhere('user_id', $user->id)['place'] }}/{{ $group->leaderboards->firstWhere('for', 'forever')->leaderboard->pluck('place')->max() }} Overall,
                                        @if($group->leaderboards->firstWhere('for', 'month') && $group->leaderboards->firstWhere('for', 'month')->leaderboard->firstWhere('user_id', $user->id))
                                        {{ $group->leaderboards->firstWhere('for', 'month')->leaderboard->firstWhere('user_id', $user->id)['place'] }}/{{ $group->leaderboards->firstWhere('for', 'month')->leaderboard->pluck('place')->max() }} This Month
                                        @else
                                            none this month.
                                        @endif
                                    </li>
                                    @endif
                                @endif
                            </ul>
                        </div>


                    </div>
                </a>
            </li>
        @endforeach

    </ul>
</div>
