<div>
    {{-- Score cards --}}
    @if($scores->isNotEmpty())
        @if($viewMode === 'tiles')
            {{-- Tiles view --}}
            <div class="p-6">
                <ul role="list" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach($scores as $score)
                        <x-score.card
                            :score="$score"
                            :show-user="true"
                            :anonymize-private-users="$anonymizePrivateUsers"
                        />
                    @endforeach
                </ul>
            </div>
        @else
            {{-- List/accordion view --}}
            <div class="divide-y divide-zinc-100">
                @foreach($scores as $score)
                    <div x-data="{ expanded: {{ $loop->index < 3 ? 'true' : 'false' }} }" class="group">
                        <button
                            type="button"
                            @click="expanded = !expanded"
                            class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-zinc-50 transition"
                        >
                            <div class="flex items-center gap-4">
                                <div class="font-semibold text-zinc-900 tabular-nums">#{{ $score->board_number }}</div>
                                <div class="text-sm text-zinc-500">{{ $score->date->format('M j') }}</div>
                                <div class="font-semibold text-zinc-900">{{ $score->score === 7 ? 'X' : $score->score }}/6{{ $score->hard_mode ? '*' : '' }}</div>
                                @if($anonymizePrivateUsers && !$score->user->public_profile)
                                    <span class="text-sm text-zinc-400">Anonymous</span>
                                @else
                                    <span class="text-sm text-zinc-600">{{ $score->user->name }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                @if(($score->comments_count ?? 0) > 0)
                                    <span class="text-xs text-zinc-400">{{ $score->comments_count }} {{ Str::plural('comment', $score->comments_count) }}</span>
                                @endif
                                <svg
                                    class="w-5 h-5 text-zinc-400 transition-transform duration-200"
                                    :class="{ 'rotate-180': expanded }"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </div>
                        </button>
                        <div
                            x-show="expanded"
                            x-collapse
                            class="px-6 pb-4"
                        >
                            <a href="{{ route('score.share-page', $score) }}" class="block">
                                <div class="flex justify-center py-4">
                                    @if($score->board)
                                        <x-score.board :score="$score"/>
                                    @else
                                        <div class="text-sm text-zinc-500">No board recorded.</div>
                                    @endif
                                </div>
                            </a>
                            <div class="flex items-center justify-between pt-2 border-t border-zinc-100">
                                <div class="text-xs text-zinc-400">
                                    {{ $score->created_at->diffForHumans() }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <livewire:score.share :icon-size="3" :score="$score" :key="'list-'.$score->id" :confirm="true"/>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @else
        <div class="p-6 text-center text-sm text-zinc-500">
            No scores recorded yet.
        </div>
    @endif

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
