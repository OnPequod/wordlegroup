<div class="rounded-2xl bg-white border border-zinc-200 shadow-sm overflow-hidden">
    {{-- Header row: title + filter --}}
    <div class="flex items-center justify-between px-6 py-5 border-b border-zinc-100">
        <div>
            <h3 class="text-lg font-semibold text-zinc-900">Group Activity</h3>
            <p class="text-sm text-zinc-500 mt-0.5">Recent scores from the group</p>
        </div>
        @if($isGroupMember)
            <div class="flex items-center gap-3">
                <div class="w-44">
                    <x-group.user-select
                        :default-empty="true"
                        wire:model.live="filterByUserId"
                        name="activityUsers"
                        label="Filter by user"
                        :group="$group"
                    />
                </div>
                @if($filterByUserId)
                    <button
                        class="text-sm text-zinc-500 hover:text-zinc-900 transition"
                        type="button"
                        wire:click="clearUserFilter"
                        x-data
                        @click="$dispatch('cleared-activity-feed-filter')"
                    >
                        Clear
                    </button>
                @endif
            </div>
        @endif
    </div>

    {{-- Activity list --}}
    <ul role="list" class="divide-y divide-zinc-100">
        @foreach($scores as $score)
            <li>
                <a
                    href="{{ route('score.share-page', $score) }}"
                    class="flex items-center gap-4 px-6 py-4 hover:bg-zinc-50/50 transition"
                >
                    {{-- Left: Avatar --}}
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center font-semibold text-sm">
                        {{ substr($score->user->name, 0, 1) }}
                    </div>

                    {{-- Middle: Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-zinc-900 leading-tight">
                            @if($anonymizePrivateUsers && $score->user->private_profile)
                                Anonymous User
                            @else
                                {{ $score->user->name }}
                            @endif
                            <span class="font-normal text-zinc-600">recorded a</span>
                            <span class="font-semibold">{{ $score->score === 7 ? 'X' : $score->score }}/6{{ $score->hard_mode ? '*' : '' }}</span>
                            <span class="font-normal text-zinc-600">on</span>
                            <span class="font-semibold">Wordle {{ number_format($score->board_number) }}</span>
                        </p>
                        <p class="text-xs text-zinc-500 mt-0.5">{{ $score->created_at->diffForHumans() }}</p>
                    </div>

                    {{-- Right: Mini Wordle grid --}}
                    <div class="flex-shrink-0 rounded-lg bg-zinc-50 border border-zinc-200 p-2">
                        @if($score->boardCanBeSeenByUser($user))
                            <div class="text-xs leading-none">
                                <x-score.board :score="$score"/>
                            </div>
                        @else
                            <div class="text-xs leading-none">
                                <x-score.hidden-board :score="$score" />
                            </div>
                            <div class="flex items-center justify-center text-zinc-400 mt-1">
                                <x-icon-solid.lock class="h-2.5 w-2.5" />
                            </div>
                        @endif
                    </div>
                </a>
            </li>
        @endforeach
    </ul>

    {{-- Pagination --}}
    @if($scores->hasPages())
        <div class="px-6 py-4 border-t border-zinc-100 bg-zinc-50/30">
            <div class="flex items-center justify-between">
                <p class="text-sm text-zinc-500">
                    Showing {{ $scores->firstItem() }}–{{ $scores->lastItem() }} of {{ $scores->total() }} scores
                </p>
                <div class="flex items-center gap-2">
                    {{ $scores->onEachSide(1)->links('vendor.pagination.simple-tailwind') }}
                </div>
            </div>
        </div>
    @endif
</div>
