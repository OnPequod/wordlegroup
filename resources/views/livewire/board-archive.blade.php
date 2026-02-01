<x-layout.page-container title="Wordle Answer Archive" :wide="true" :top-padding="false">

    <x-layout.social-meta
        title="Wordle Answer Archive - All Past Answers"
        :url="route('board.archive')"
        description="Complete archive of all {{ $this->totalPuzzles }} past Wordle answers. Browse by date, search the history, and see stats for every puzzle."
    />

    <div class="flex flex-col gap-6 pb-12">
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 font-serif">Wordle Answer Archive</h1>
            <p class="mt-1 text-sm text-zinc-500">
                {{ number_format($this->totalPuzzles) }} past puzzles since June 19, 2021
            </p>
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap items-center gap-3">
            {{-- Year filter --}}
            <select
                wire:model.live="year"
                class="rounded-lg border border-zinc-200 bg-white pl-3 pr-8 py-2 text-sm text-zinc-700 focus:border-green-600 focus:ring-green-600"
            >
                <option value="">All Years</option>
                @foreach($this->availableYears as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>

            {{-- Month filter --}}
            @if($year)
                <select
                    wire:model.live="month"
                    class="rounded-lg border border-zinc-200 bg-white pl-3 pr-8 py-2 text-sm text-zinc-700 focus:border-green-600 focus:ring-green-600"
                >
                    <option value="">All Months</option>
                    @foreach($this->availableMonths as $m => $name)
                        <option value="{{ $m }}">{{ $name }}</option>
                    @endforeach
                </select>
            @endif

            @if($year || $month)
                <button
                    wire:click="clearFilters"
                    class="text-sm text-zinc-500 hover:text-zinc-700 underline"
                >
                    Clear filters
                </button>
            @endif

            <span class="text-sm text-zinc-400 ml-auto">
                Showing: {{ $this->pageTitle }}
            </span>
        </div>

        {{-- Puzzle grid --}}
        <div class="bg-white rounded-xl border border-zinc-200 overflow-hidden">
            <table class="min-w-full divide-y divide-zinc-100">
                <thead class="bg-zinc-50">
                    <tr>
                        <th scope="col" class="py-3 pl-4 pr-2 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">#</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Date</th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Answer</th>
                        <th scope="col" class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">Avg</th>
                        <th scope="col" class="py-3 pl-3 pr-4 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($this->puzzles as $puzzle)
                        <tr class="hover:bg-zinc-50 transition">
                            <td class="py-3 pl-4 pr-2 whitespace-nowrap text-sm text-zinc-500 tabular-nums">
                                {{ $puzzle->board_number }}
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-zinc-700">
                                {{ $puzzle->puzzle_date->format('M j, Y') }}
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap">
                                @if($this->canViewAnswer($puzzle->board_number))
                                    <span class="font-mono font-bold text-zinc-900 tracking-wider">{{ $puzzle->answer }}</span>
                                @else
                                    <span x-data="{ revealed: false }" class="inline-flex items-center gap-1">
                                        <span
                                            class="font-mono font-bold tracking-wider cursor-pointer select-none"
                                            :class="revealed ? 'text-zinc-900' : 'text-zinc-400'"
                                            @click="revealed = !revealed"
                                            title="Click to reveal"
                                        >
                                            <span x-show="!revealed">&bull;&bull;&bull;&bull;&bull;</span>
                                            <span x-show="revealed" x-cloak>{{ $puzzle->answer }}</span>
                                        </span>
                                        <button
                                            @click="revealed = !revealed"
                                            class="text-zinc-400 hover:text-zinc-600 transition"
                                            :title="revealed ? 'Hide answer' : 'Reveal answer'"
                                        >
                                            <svg x-show="!revealed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="revealed" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-right text-sm tabular-nums">
                                @if($summary = $this->summaries->get($puzzle->board_number))
                                    <span class="font-semibold text-zinc-700">{{ $summary->wg_score_mean }}</span>
                                @else
                                    <span class="text-zinc-300">&mdash;</span>
                                @endif
                            </td>
                            <td class="py-3 pl-3 pr-4 whitespace-nowrap text-right">
                                <a
                                    href="{{ route('board', $puzzle->board_number) }}"
                                    class="text-sm text-green-700 hover:text-green-800 font-medium"
                                >
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-zinc-500">
                                No puzzles found for this period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($this->puzzles->hasPages())
            <div class="mt-4">
                {{ $this->puzzles->links() }}
            </div>
        @endif

        {{-- SEO content --}}
        <div class="prose prose-zinc max-w-none mt-8">
            <h2 class="text-lg font-semibold text-zinc-900">About the Wordle Archive</h2>
            <p class="text-sm text-zinc-600">
                This archive contains every Wordle answer since the game launched on June 19, 2021 with puzzle #0 (CIGAR).
                Wordle was created by Josh Wardle and acquired by The New York Times in January 2022.
                Each day features a new five-letter word that players have six attempts to guess.
            </p>
            <p class="text-sm text-zinc-600">
                Click "View" on any puzzle to see detailed statistics including average scores, score distribution, and player boards from our community.
            </p>
        </div>

        {{-- Quick Links --}}
        <x-layout.quick-links />
    </div>

</x-layout.page-container>
