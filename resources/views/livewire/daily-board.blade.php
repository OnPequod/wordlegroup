<x-layout.page-container title="Wordle #{{ $boardNumber }}" :wide="true" :top-padding="false">

    <x-layout.social-meta
        title="Wordle #{{ $boardNumber }} - Daily Stats"
        :url="route('board', $boardNumber)"
        description="See stats and results for Wordle #{{ $boardNumber }}."
    />

    <div class="flex flex-col gap-8 pb-12">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-zinc-900 font-serif">Wordle #{{ $boardNumber }}</h1>
                    @if($this->isCurrentPuzzle)
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
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
                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-zinc-200 bg-white text-zinc-600 hover:bg-zinc-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
                    title="Previous puzzle"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                @if(!$this->isCurrentPuzzle)
                    <button
                        wire:click="goToToday"
                        class="inline-flex items-center justify-center px-4 h-10 rounded-lg border border-zinc-200 bg-white text-sm font-medium text-zinc-600 hover:bg-zinc-50 transition"
                    >
                        Today
                    </button>
                @endif

                <button
                    wire:click="goToNext"
                    @if(!$this->hasNext) disabled @endif
                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-zinc-200 bg-white text-zinc-600 hover:bg-zinc-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
                    title="Next puzzle"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Answer Card --}}
        @if($this->puzzle)
            <div class="flex justify-center">
                <div class="rounded-2xl bg-white border border-zinc-200 shadow-sm px-8 py-6 text-center">
                    <p class="text-xs font-semibold uppercase tracking-wider text-zinc-400 mb-2">{{ $this->isCurrentPuzzle ? "Today's Answer" : 'Answer' }}</p>
                    @if($this->canViewAnswer)
                        <p class="text-3xl font-bold text-zinc-900 tracking-widest font-mono">{{ $this->puzzle->answer }}</p>
                    @else
                        <div x-data="{ revealed: false }">
                            <p
                                class="text-3xl font-bold tracking-widest font-mono cursor-pointer select-none transition-colors"
                                :class="revealed ? 'text-zinc-900' : 'text-zinc-300'"
                                @click="revealed = !revealed"
                            >
                                <span x-show="!revealed">&bull;&bull;&bull;&bull;&bull;</span>
                                <span x-show="revealed" x-cloak>{{ $this->puzzle->answer }}</span>
                            </p>
                            <button
                                @click="revealed = !revealed"
                                class="mt-3 inline-flex items-center gap-1.5 text-sm text-zinc-500 hover:text-zinc-700 transition"
                            >
                                <svg x-show="!revealed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="revealed" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                                <span x-text="revealed ? 'Hide answer' : 'Reveal answer'"></span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if($this->summary)
            {{-- Stats based on ALL WordleGroup users --}}
            @if($this->summary->wg_participant_count)
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                    <div class="rounded-xl bg-white border border-zinc-200 p-4">
                        <p class="text-sm font-medium text-zinc-500">Players</p>
                        <p class="mt-1 text-2xl font-semibold text-zinc-900 tabular-nums">{{ $this->summary->wg_participant_count }}</p>
                    </div>

                    <div class="rounded-xl bg-white border border-zinc-200 p-4">
                        <p class="text-sm font-medium text-zinc-500">Average</p>
                        <p class="mt-1 text-2xl font-semibold text-zinc-900 tabular-nums">{{ $this->summary->wg_score_mean }}</p>
                    </div>

                    <div class="rounded-xl bg-white border border-zinc-200 p-4">
                        <p class="text-sm font-medium text-zinc-500">Difficulty</p>
                        @if($this->summary->difficultyLabel)
                            <p class="mt-1 text-2xl font-semibold {{ $this->summary->difficultyColor }}">
                                {{ $this->summary->difficultyLabel }}
                            </p>
                            @if($this->summary->difficulty_delta !== null)
                                <p class="text-xs text-zinc-400 mt-0.5">
                                    {{ $this->summary->difficulty_delta >= 0 ? '+' : '' }}{{ number_format($this->summary->difficulty_delta, 2) }} vs avg
                                </p>
                            @endif
                        @else
                            <p class="mt-1 text-2xl font-semibold text-zinc-400">N/A</p>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Bot scores (if available) --}}
            @if($this->summary->bot_skill_mean || $this->summary->bot_luck_mean)
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 sm:max-w-md">
                    @if($this->summary->bot_skill_mean)
                        <div class="rounded-xl bg-white border border-zinc-200 p-4">
                            <p class="text-sm font-medium text-zinc-500">Avg Skill</p>
                            <p class="mt-1 text-xl font-semibold text-zinc-900 tabular-nums">{{ $this->summary->bot_skill_mean }}/99</p>
                        </div>
                    @endif

                    @if($this->summary->bot_luck_mean)
                        <div class="rounded-xl bg-white border border-zinc-200 p-4">
                            <p class="text-sm font-medium text-zinc-500">Avg Luck</p>
                            <p class="mt-1 text-xl font-semibold text-zinc-900 tabular-nums">{{ $this->summary->bot_luck_mean }}/99</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Score distribution (based on all WordleGroup users) --}}
            @if($this->summary->formattedWgDistribution)
                <div class="rounded-xl bg-white border border-zinc-200 p-6">
                    <h2 class="text-lg font-semibold text-zinc-900 mb-4">Score Distribution</h2>
                    <div class="space-y-2">
                        @foreach($this->summary->formattedWgDistribution as $score => $data)
                            <div class="flex items-center gap-3">
                                <span class="w-6 text-sm font-medium text-zinc-600 text-right tabular-nums">{{ $score }}</span>
                                <div class="flex-1 h-6 bg-zinc-100 rounded overflow-hidden">
                                    <div
                                        class="h-full rounded {{ $score === 'X' ? 'bg-zinc-400' : 'bg-green-600' }} transition-all duration-300"
                                        style="width: {{ $data['percentage'] }}%"
                                    ></div>
                                </div>
                                <span class="w-24 text-sm tabular-nums text-right whitespace-nowrap">
                                    <span class="font-semibold text-zinc-700">{{ $data['count'] }}</span>
                                    <span class="text-zinc-400">{{ $data['percentage'] }}%</span>
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Boards grid (only public leaderboard participants) --}}
            @if($this->summary->boards && $this->summary->boards->isNotEmpty())
                <div class="rounded-xl bg-white border border-zinc-200 p-6">
                    <h2 class="text-lg font-semibold text-zinc-900 mb-4">
                        Public Leaderboard
                        @if($this->isCurrentPuzzle && !$this->canViewBoards)
                            <span class="text-sm font-normal text-zinc-500 ml-2">(Hidden until you record your score)</span>
                        @endif
                    </h2>

                    @if($this->canViewBoards)
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                            @foreach($this->summary->boards as $board)
                                <div class="p-3 rounded-lg bg-zinc-50 border border-zinc-100">
                                    <div class="text-sm font-medium text-zinc-700 mb-2 truncate">
                                        @if($board['name'])
                                            {{ $board['name'] }}
                                        @else
                                            <span class="text-zinc-400 italic">Anonymous</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-zinc-500 mb-2">
                                        {{ $board['score'] === 7 ? 'X' : $board['score'] }}/6{{ $board['hard_mode'] ? '*' : '' }}
                                        @if($board['bot_skill'] !== null)
                                            <span class="ml-1">S:{{ $board['bot_skill'] }}</span>
                                        @endif
                                        @if($board['bot_luck'] !== null)
                                            <span class="ml-1">L:{{ $board['bot_luck'] }}</span>
                                        @endif
                                    </div>
                                    <div class="leading-tight text-xs">
                                        @php
                                            $boardHtml = $board['board'];
                                            $boardHtml = str_replace('🟩', '<span class="inline-block w-3 h-3 m-0.5 rounded-sm bg-green-600"></span>', $boardHtml);
                                            $boardHtml = str_replace('🟨', '<span class="inline-block w-3 h-3 m-0.5 rounded-sm bg-yellow-500"></span>', $boardHtml);
                                            $boardHtml = str_replace('⬜', '<span class="inline-block w-3 h-3 m-0.5 rounded-sm bg-zinc-300"></span>', $boardHtml);
                                            $boardHtml = str_replace("\r\n", '<br>', $boardHtml);
                                            $boardHtml = str_replace("\n", '<br>', $boardHtml);
                                        @endphp
                                        {!! $boardHtml !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-zinc-400 mb-2">
                                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <p class="text-zinc-500">Record your score for today's puzzle to see other players' boards.</p>
                            @guest
                                <a href="{{ route('login') }}" class="mt-4 inline-block text-green-700 font-medium hover:underline">
                                    Log in to record your score
                                </a>
                            @else
                                <a href="{{ route('account.record-score') }}" class="mt-4 inline-block text-green-700 font-medium hover:underline">
                                    Record your score
                                </a>
                            @endguest
                        </div>
                    @endif
                </div>
            @endif
        @else
            {{-- No data available --}}
            <div class="rounded-xl bg-white border border-zinc-200 p-8 text-center">
                <p class="text-zinc-500">No data available for this puzzle yet.</p>
                <p class="text-sm text-zinc-400 mt-2">
                    @if($this->isCurrentPuzzle)
                        Check back later as players record their scores.
                    @else
                        This puzzle may not have had any opted-in participants.
                    @endif
                </p>
            </div>
        @endif

        {{-- Comments Section --}}
        <div class="rounded-xl bg-white border border-zinc-200 p-6">
            <h2 class="text-lg font-semibold text-zinc-900 mb-4">
                Discussion
                @if($this->comments->count())
                    <span class="text-sm font-normal text-zinc-400">({{ $this->comments->count() }})</span>
                @endif
            </h2>

            {{-- Comment Form --}}
            @auth
                <form wire:submit="addComment" class="mb-6">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-800 font-semibold text-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </span>
                        </div>
                        <div class="flex-1">
                            <textarea
                                wire:model="newComment"
                                placeholder="Share your thoughts on today's puzzle..."
                                rows="2"
                                class="w-full rounded-lg border border-zinc-200 px-3 py-2 text-sm focus:border-green-600 focus:ring-green-600 resize-none"
                            ></textarea>
                            @error('newComment')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <div class="mt-2 flex justify-end">
                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800 disabled:opacity-50"
                                >
                                    <span wire:loading.remove wire:target="addComment">Post</span>
                                    <span wire:loading wire:target="addComment">Posting...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <div class="mb-6 p-4 rounded-lg bg-zinc-50 text-center">
                    <p class="text-sm text-zinc-600">
                        <a href="{{ route('login') }}" class="text-green-700 font-medium hover:underline">Log in</a>
                        to join the discussion.
                    </p>
                </div>
            @endauth

            {{-- Comments List --}}
            @if($this->comments->isNotEmpty())
                <div class="space-y-4">
                    @foreach($this->comments as $comment)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-zinc-100 text-zinc-600 font-semibold text-sm">
                                    {{ substr($comment->user->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-zinc-900 text-sm">{{ $comment->user->name }}</span>
                                    <span class="text-xs text-zinc-400">{{ $comment->created_at->diffForHumans() }}</span>
                                    @if($comment->canBeDeletedBy(Auth::user()))
                                        <span class="text-zinc-300">&middot;</span>
                                        <button
                                            wire:click="deleteComment({{ $comment->id }})"
                                            wire:confirm="Are you sure you want to delete this comment?"
                                            class="text-xs text-zinc-400 hover:text-red-600 cursor-pointer transition"
                                        >
                                            Delete
                                        </button>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-zinc-700 whitespace-pre-wrap break-words">{{ $comment->body }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-zinc-400 text-center py-4">No comments yet. Be the first to share your thoughts!</p>
            @endif
        </div>

        {{-- Quick Links --}}
        <x-layout.quick-links />
    </div>

</x-layout.page-container>
