<x-layout.page-container title="Wordle #{{ $boardNumber }}" :wide="true" :top-padding="false">

    <x-layout.social-meta
        title="Wordle #{{ $boardNumber }} - {{ $this->puzzleDate->format('M j, Y') }}"
        :url="route('puzzle', $boardNumber)"
        description="See how players performed on Wordle #{{ $boardNumber }}."
    />
    <x-layout.json-ld :schema="[
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => 'Wordle #' . $boardNumber . ' - ' . $this->puzzleDate->format('M j, Y'),
        'description' => 'See how players performed on Wordle #' . $boardNumber . '.',
        'url' => route('puzzle', $boardNumber),
        'datePublished' => $this->puzzleDate->toIso8601String(),
        'isPartOf' => ['@id' => url('/') . '#website'],
    ]" />

    <div class="flex flex-col gap-6 pb-12">
        {{-- Header with navigation --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-zinc-900 font-serif">Wordle #{{ $boardNumber }}</h1>
                    @if($this->isCurrentPuzzle)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Today
                        </span>
                    @endif
                </div>
                <p class="mt-1 text-sm text-zinc-500">
                    {{ $this->puzzleDate->format('l, F j, Y') }}
                </p>
            </div>

            {{-- Navigation --}}
            <div class="flex items-center gap-2">
                <button
                    wire:click="goToPrevious"
                    @if(!$this->hasPrevious) disabled @endif
                    class="p-2 rounded-lg border border-zinc-200 text-zinc-600 hover:bg-zinc-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    title="Previous puzzle"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                @if(!$this->isCurrentPuzzle)
                    <button
                        wire:click="goToToday"
                        class="px-3 py-2 rounded-lg border border-zinc-200 text-sm font-medium text-zinc-600 hover:bg-zinc-50"
                    >
                        Today
                    </button>
                @endif

                <button
                    wire:click="goToNext"
                    @if(!$this->hasNext) disabled @endif
                    class="p-2 rounded-lg border border-zinc-200 text-zinc-600 hover:bg-zinc-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    title="Next puzzle"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        @if($this->summary)
            {{-- Stats cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                {{-- Players --}}
                <div class="rounded-xl bg-white border border-zinc-200 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Players</div>
                    <div class="mt-1 text-2xl font-bold text-zinc-900 tabular-nums">
                        {{ $this->summary->wg_participant_count ?? $this->summary->participant_count ?? 0 }}
                    </div>
                </div>

                {{-- Average Score --}}
                <div class="rounded-xl bg-white border border-zinc-200 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Avg Score</div>
                    <div class="mt-1 text-2xl font-bold text-zinc-900 tabular-nums">
                        {{ $this->summary->wg_score_mean ?? $this->summary->score_mean ?? '—' }}
                    </div>
                </div>

                {{-- Difficulty --}}
                <div class="rounded-xl bg-white border border-zinc-200 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Difficulty</div>
                    <div class="mt-1 text-2xl font-bold {{ $this->summary->difficultyColor }}">
                        {{ $this->summary->difficultyLabel ?? '—' }}
                    </div>
                    @if($this->summary->difficulty_delta !== null)
                        <div class="text-xs text-zinc-500 mt-1">
                            {{ $this->summary->difficulty_delta > 0 ? '+' : '' }}{{ number_format($this->summary->difficulty_delta, 2) }} vs avg
                        </div>
                    @endif
                </div>

                {{-- Median --}}
                <div class="rounded-xl bg-white border border-zinc-200 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Median</div>
                    <div class="mt-1 text-2xl font-bold text-zinc-900 tabular-nums">
                        {{ $this->summary->score_median ?? '—' }}
                    </div>
                </div>
            </div>

            {{-- Bot scores if available --}}
            @if($this->summary->bot_skill_mean || $this->summary->bot_luck_mean)
                <div class="grid grid-cols-2 gap-4">
                    @if($this->summary->bot_skill_mean)
                        <div class="rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 p-4">
                            <div class="text-xs font-semibold uppercase tracking-wider text-blue-700">Avg Skill</div>
                            <div class="mt-1 text-2xl font-bold text-blue-900 tabular-nums">
                                {{ $this->summary->bot_skill_mean }}/99
                            </div>
                        </div>
                    @endif
                    @if($this->summary->bot_luck_mean)
                        <div class="rounded-xl bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-100 p-4">
                            <div class="text-xs font-semibold uppercase tracking-wider text-purple-700">Avg Luck</div>
                            <div class="mt-1 text-2xl font-bold text-purple-900 tabular-nums">
                                {{ $this->summary->bot_luck_mean }}/99
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Distribution chart --}}
            @php
                $distribution = $this->summary->formattedDistribution;
                $maxPercentage = collect($distribution)->max('percentage') ?: 1;
            @endphp
            @if(!empty($distribution))
                <div class="rounded-xl bg-white border border-zinc-200 p-6">
                    <h3 class="text-sm font-semibold text-zinc-900 mb-4">Score Distribution</h3>
                    <div class="space-y-2">
                        @foreach($distribution as $score => $data)
                            <div class="flex items-center gap-3">
                                <div class="w-6 text-sm font-medium text-zinc-600 text-right">{{ $score }}</div>
                                <div class="flex-1 h-6 bg-zinc-100 rounded overflow-hidden">
                                    <div
                                        class="h-full {{ $score === 'X' ? 'bg-zinc-400' : 'bg-green-600' }} rounded transition-all duration-300"
                                        style="width: {{ $maxPercentage > 0 ? ($data['percentage'] / $maxPercentage) * 100 : 0 }}%"
                                    ></div>
                                </div>
                                <div class="w-16 text-sm text-zinc-500 text-right tabular-nums">
                                    {{ $data['count'] }} <span class="text-zinc-400">({{ $data['percentage'] }}%)</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Boards grid --}}
            @if($this->canViewBoards && $this->summary->boards && $this->summary->boards->isNotEmpty())
                <div class="rounded-xl bg-white border border-zinc-200 p-6">
                    <h3 class="text-sm font-semibold text-zinc-900 mb-4">Player Boards</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($this->summary->boards as $board)
                            <div class="flex flex-col items-center text-center">
                                <div class="text-xs text-zinc-500 mb-1 truncate max-w-full">
                                    @if($board['show_name'] ?? false)
                                        {{ $board['name'] }}
                                    @else
                                        <span class="italic text-zinc-400">Anonymous</span>
                                    @endif
                                </div>
                                <div class="text-sm font-semibold text-zinc-900 mb-2">{{ $board['score'] }}/6{{ ($board['hard_mode'] ?? false) ? '*' : '' }}</div>
                                @if($board['board'])
                                    <x-score.board :board="$board['board']" :small="true" />
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif($this->isCurrentPuzzle && !$this->userHasRecorded)
                <div class="rounded-xl bg-amber-50 border border-amber-200 p-6 text-center">
                    <div class="text-amber-800 font-medium">Boards are hidden for today's puzzle</div>
                    <p class="text-sm text-amber-600 mt-1">Record your score to see how others did!</p>
                    <a href="{{ route('account') }}" class="inline-block mt-3 px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-medium hover:bg-amber-700">
                        Record Your Score
                    </a>
                </div>
            @endif

        @else
            <div class="rounded-xl bg-white border border-zinc-200 p-8 text-center">
                <p class="text-zinc-500">No data available for this puzzle yet.</p>
                <p class="text-sm text-zinc-400 mt-2">Check back later as players record their scores.</p>
            </div>
        @endif

        {{-- Link to leaderboard --}}
        <div class="text-center">
            <a href="{{ route('leaderboard') }}" class="text-sm text-green-700 hover:text-green-800 font-medium">
                View Public Leaderboard &rarr;
            </a>
        </div>
    </div>

</x-layout.page-container>
