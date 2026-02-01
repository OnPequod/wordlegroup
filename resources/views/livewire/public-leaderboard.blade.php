<x-layout.page-container title="Wordle Public Leaderboard" :wide="true" :top-padding="false">

    <x-layout.social-meta
        title="Wordle Public Leaderboard"
        :url="route('leaderboard')"
        description="See how you rank against other Wordle players worldwide."
    />

    <div class="flex flex-col gap-8 pb-12">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 font-serif">Public Leaderboard</h1>
                <p class="mt-1 text-sm text-zinc-500">
                    @if($this->leaderboard && $this->leaderboard->participant_count)
                        {{ $this->leaderboard->participant_count }} {{ Str::plural('player', $this->leaderboard->participant_count) }} ranked
                    @else
                        See how you rank against other players
                    @endif
                </p>
            </div>
            @guest
                <div class="flex flex-wrap items-center gap-3">
                    <a
                        class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800"
                        href="{{ route('login') }}"
                    >Log in to join</a>
                </div>
            @endguest
        </div>

        {{-- Inline settings form for logged-in users --}}
        @auth
            <div class="rounded-2xl bg-white border border-zinc-200 shadow-sm p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-zinc-900">Your Leaderboard Settings</h3>
                        <p class="mt-1 text-sm text-zinc-500">Adjust how you appear on the public leaderboard.</p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 items-end">
                    {{-- Public Alias --}}
                    <div>
                        <label for="publicAlias" class="block text-sm font-medium text-zinc-700 mb-1">Public Alias</label>
                        <input
                            type="text"
                            id="publicAlias"
                            wire:model="publicAlias"
                            placeholder="Your display name"
                            class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm focus:border-green-600 focus:ring-green-600"
                        >
                    </div>

                    {{-- Participate in leaderboard --}}
                    <label
                        for="showOnPublicLeaderboard"
                        class="flex items-center gap-3 py-2 px-3 rounded-lg cursor-pointer hover:bg-zinc-50 transition border border-zinc-200"
                    >
                        <input
                            type="checkbox"
                            id="showOnPublicLeaderboard"
                            wire:model.live="showOnPublicLeaderboard"
                            class="h-4 w-4 rounded border-zinc-300 text-green-700 focus:ring-green-600"
                        >
                        <span class="text-sm text-zinc-700">Participate</span>
                    </label>

                    {{-- Display name publicly --}}
                    <label
                        for="showNameOnPublicLeaderboard"
                        class="flex items-center gap-3 py-2 px-3 rounded-lg cursor-pointer hover:bg-zinc-50 transition border border-zinc-200 {{ !$showOnPublicLeaderboard ? 'opacity-50' : '' }}"
                    >
                        <input
                            type="checkbox"
                            id="showNameOnPublicLeaderboard"
                            wire:model="showNameOnPublicLeaderboard"
                            {{ !$showOnPublicLeaderboard ? 'disabled' : '' }}
                            class="h-4 w-4 rounded border-zinc-300 text-green-700 focus:ring-green-600"
                        >
                        <span class="text-sm text-zinc-700">Display name</span>
                    </label>

                    {{-- Save button --}}
                    <button
                        type="button"
                        wire:click="saveSettings"
                        wire:loading.attr="disabled"
                        class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800 disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="saveSettings">Save</span>
                        <span wire:loading wire:target="saveSettings">Saving...</span>
                    </button>
                </div>

                @if($showOnPublicLeaderboard && $showNameOnPublicLeaderboard)
                    <p class="mt-3 text-xs text-zinc-500">
                        You'll appear as: <strong class="text-zinc-700">{{ $publicAlias ?: Auth::user()->name }}</strong>
                    </p>
                @elseif($showOnPublicLeaderboard)
                    <p class="mt-3 text-xs text-zinc-500">
                        You'll appear as: <strong class="text-zinc-400 italic">Anonymous</strong>
                    </p>
                @else
                    <p class="mt-3 text-xs text-zinc-500">
                        Your scores are not included on the public leaderboard.
                    </p>
                @endif
            </div>
        @endauth

        {{-- Today's Puzzle & WordleGroup Average --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {{-- Today's Puzzle --}}
            @if($this->todaysPuzzle)
                <a href="{{ route('board') }}" class="rounded-xl bg-gradient-to-br from-green-600 to-green-700 p-5 text-white hover:from-green-700 hover:to-green-800 transition shadow-sm">
                    <div class="text-sm font-medium text-green-100">Today's Puzzle</div>
                    <div class="mt-1 flex items-baseline gap-2">
                        <span class="text-2xl font-bold">#{{ $this->todaysPuzzle->board_number }}</span>
                        <span class="text-green-200">{{ $this->todaysPuzzle->puzzle_date->format('M j') }}</span>
                    </div>
                    <div class="mt-2 text-sm text-green-100">View stats &rarr;</div>
                </a>
            @endif

            {{-- WordleGroup Average --}}
            @if($this->todaysSummary && $this->todaysSummary->wg_participant_count)
                <a href="{{ route('board') }}" class="rounded-xl bg-gradient-to-br from-emerald-50 to-green-100 border border-green-200 p-5 hover:from-emerald-100 hover:to-green-200 transition shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wider text-green-700">WordleGroup Average</div>
                    <div class="mt-1 text-3xl font-bold text-green-900 tabular-nums">{{ $this->todaysSummary->wg_score_mean }}</div>
                    <div class="mt-2 text-sm text-green-700">{{ $this->todaysSummary->wg_participant_count }} {{ Str::plural('player', $this->todaysSummary->wg_participant_count) }} today</div>
                </a>
            @endif

            {{-- Recent Answers --}}
            <div class="lg:col-span-2 rounded-xl bg-white border border-zinc-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-zinc-900">Recent Answers</h3>
                    <a href="{{ route('board.archive') }}" class="text-xs text-green-700 hover:text-green-800 font-medium">
                        View all &rarr;
                    </a>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($this->recentPuzzles as $puzzle)
                        <span x-data="{ revealed: false }" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-zinc-50 border border-zinc-100">
                            <a
                                href="{{ route('board', $puzzle->board_number) }}"
                                class="text-xs text-zinc-400 hover:text-zinc-600"
                            >#{{ $puzzle->board_number }}</a>
                            @if($this->canViewAnswer($puzzle->board_number))
                                <a href="{{ route('board', $puzzle->board_number) }}" class="font-mono font-bold text-sm text-zinc-800 hover:text-zinc-900">{{ $puzzle->answer }}</a>
                            @else
                                <span
                                    class="font-mono font-bold text-sm cursor-pointer select-none"
                                    :class="revealed ? 'text-zinc-800' : 'text-zinc-400'"
                                    @click="revealed = !revealed"
                                    title="Click to reveal"
                                >
                                    <span x-show="!revealed">&bull;&bull;&bull;&bull;&bull;</span>
                                    <span x-show="revealed" x-cloak>{{ $puzzle->answer }}</span>
                                </span>
                                <button
                                    @click="revealed = !revealed"
                                    class="text-zinc-400 hover:text-zinc-600 transition"
                                    :title="revealed ? 'Hide' : 'Reveal'"
                                >
                                    <svg x-show="!revealed" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="revealed" x-cloak class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            @endif
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Period tabs --}}
        <div class="flex justify-start">
            <div class="inline-flex rounded-lg border border-zinc-200 bg-white p-1">
                <button
                    wire:click="setPeriod('forever')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition {{ $period === 'forever' ? 'bg-green-700 text-white' : 'text-zinc-600 hover:bg-zinc-50' }}"
                >
                    All Time
                </button>
                <button
                    wire:click="setPeriod('month')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition {{ $period === 'month' ? 'bg-green-700 text-white' : 'text-zinc-600 hover:bg-zinc-50' }}"
                >
                    This Month
                </button>
                <button
                    wire:click="setPeriod('week')"
                    class="px-4 py-2 text-sm font-medium rounded-md transition {{ $period === 'week' ? 'bg-green-700 text-white' : 'text-zinc-600 hover:bg-zinc-50' }}"
                >
                    This Week
                </button>
            </div>
        </div>

        @if($this->leaderboard && $this->leaderboard->leaderboard && $this->leaderboard->leaderboard->isNotEmpty())
            <div class="bg-white rounded-xl border border-zinc-200/70 shadow-sm overflow-hidden">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-zinc-100">
                            <th scope="col" class="py-3 pl-4 pr-2 w-16 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Place</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Name</th>
                            <th scope="col" class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500 whitespace-nowrap">Avg. Score</th>
                            <th scope="col" class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">Games</th>
                            <th scope="col" class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500 whitespace-nowrap hidden sm:table-cell">Skill Avg</th>
                            <th scope="col" class="py-3 pl-3 pr-4 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500 whitespace-nowrap hidden sm:table-cell">Luck Avg</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach($this->leaderboard->leaderboard as $position)
                            <tr class="{{ $position['place'] === 1 ? 'bg-amber-50/40' : '' }}">
                                <td class="py-3.5 pl-4 pr-2 whitespace-nowrap">
                                    @if($position['place'] === 1)
                                        <span class="inline-flex justify-center items-center w-8 h-8 text-sm font-bold text-amber-900 rounded-full bg-amber-400 shadow-sm">{{ $position['place'] }}</span>
                                    @elseif($position['place'] === 2)
                                        <span class="inline-flex justify-center items-center w-8 h-8 text-sm font-bold text-zinc-700 rounded-full bg-zinc-300 shadow-sm">{{ $position['place'] }}</span>
                                    @elseif($position['place'] === 3)
                                        <span class="inline-flex justify-center items-center w-8 h-8 text-sm font-bold text-orange-900 rounded-full bg-orange-400/70 shadow-sm">{{ $position['place'] }}</span>
                                    @else
                                        <span class="inline-flex justify-center items-center w-8 h-8 text-sm font-medium text-zinc-500">{{ $position['place'] }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3.5 whitespace-nowrap {{ $position['place'] <= 3 ? 'font-semibold text-zinc-900' : 'font-medium text-zinc-700' }}">
                                    @if($position['show_name'] ?? false)
                                        {{ $position['name'] }}
                                    @else
                                        <span class="text-zinc-400 italic">Anonymous</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3.5 whitespace-nowrap text-right tabular-nums {{ $position['place'] <= 3 ? 'font-semibold text-zinc-900' : 'font-medium text-zinc-700' }}">
                                    {{ $position['stats']['mean'] }}
                                </td>
                                <td class="px-3 py-3.5 whitespace-nowrap text-right tabular-nums {{ $position['place'] <= 3 ? 'font-semibold text-zinc-900' : 'font-medium text-zinc-700' }}">
                                    {{ $position['stats']['count'] }}
                                </td>
                                <td class="px-3 py-3.5 whitespace-nowrap text-right tabular-nums text-zinc-600 hidden sm:table-cell">
                                    {!! $position['stats']['bot_skill_mean'] ?? '&#x2014;' !!}
                                </td>
                                <td class="py-3.5 pl-3 pr-4 whitespace-nowrap text-right tabular-nums text-zinc-600 hidden sm:table-cell">
                                    {!! $position['stats']['bot_luck_mean'] ?? '&#x2014;' !!}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white rounded-xl border border-zinc-200/70 shadow-sm shadow-zinc-900/5 p-8 text-center">
                <p class="text-zinc-500">No leaderboard data available yet.</p>
                <p class="text-sm text-zinc-400 mt-2">Check back later or opt in from your account settings.</p>
            </div>
        @endif
    </div>

</x-layout.page-container>
