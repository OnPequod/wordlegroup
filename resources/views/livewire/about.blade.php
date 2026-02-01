<x-layout.page-container title="About Wordle Group" :top-padding="false">
    <x-layout.social-meta
        title="About Wordle Group"
        :url="route('about')"
        description="WordleGroup is a free tool for tracking and sharing Wordle scores with friends and family."
    />

    <div class="flex flex-col gap-8 pb-12">
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 font-serif">About WordleGroup</h1>
            <p class="mt-1 text-sm text-zinc-500">
                Track your Wordle scores with friends and family
            </p>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="rounded-xl bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 p-5 text-center">
                <p class="text-3xl font-bold text-green-900 tabular-nums">{{ number_format($this->stats['users']) }}</p>
                <p class="text-sm text-green-700 mt-1">Players</p>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 p-5 text-center">
                <p class="text-3xl font-bold text-green-900 tabular-nums">{{ number_format($this->stats['groups']) }}</p>
                <p class="text-sm text-green-700 mt-1">Groups</p>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 p-5 text-center">
                <p class="text-3xl font-bold text-green-900 tabular-nums">{{ number_format($this->stats['scores']) }}</p>
                <p class="text-sm text-green-700 mt-1">Scores Recorded</p>
            </div>
        </div>

        {{-- Score Distribution --}}
        <div class="rounded-xl bg-white border border-zinc-200 p-6">
            <h2 class="text-lg font-semibold text-zinc-900 mb-4">All-Time Score Distribution</h2>
            <div class="space-y-2">
                @foreach($this->scoreDistribution as $score => $data)
                    <div class="flex items-center gap-3">
                        <span class="w-6 text-sm font-medium text-zinc-600 text-right tabular-nums">{{ $score }}</span>
                        <div class="flex-1 h-6 bg-zinc-100 rounded overflow-hidden">
                            <div
                                class="h-full rounded {{ $score === 'X' ? 'bg-zinc-400' : 'bg-green-600' }} transition-all duration-300"
                                style="width: {{ $data['percentage'] }}%"
                            ></div>
                        </div>
                        <span class="w-28 text-sm tabular-nums text-right whitespace-nowrap">
                            <span class="font-semibold text-zinc-700">{{ number_format($data['count']) }}</span>
                            <span class="text-zinc-400">{{ $data['percentage'] }}%</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Content --}}
        <div class="rounded-2xl bg-white border border-zinc-200 p-6 sm:p-8">
            <div class="prose prose-zinc max-w-none">
                <h2>What is WordleGroup?</h2>
                <p>
                    WordleGroup is a free tool that makes it easy to track and compare Wordle scores with your friends, family, or coworkers. Create a group, invite your people, and see who's really the best at Wordle.
                </p>

                <h2>Features</h2>
                <ul>
                    <li><strong>Private Groups</strong> - Create groups and invite friends to compete</li>
                    <li><strong>Leaderboards</strong> - See rankings across different time periods</li>
                    <li><strong>Statistics</strong> - Track your average, streaks, and score distribution</li>
                    <li><strong>Daily Puzzles</strong> - View community stats for each day's puzzle</li>
                    <li><strong>Public Leaderboard</strong> - Opt in to see how you rank globally</li>
                </ul>

                <h2>About</h2>
                <p>
                    WordleGroup was created by <a href="mailto:erik@pequod.sh">Erik Westlund</a> to provide an easy way to see who in our family group chat was doing the best at Wordle.
                </p>
                <p>
                    WordleGroup is not affiliated with <a href="https://www.nytimes.com/games/wordle/">Wordle</a> or <a href="https://www.nytimes.com/">The New York Times</a>.
                </p>

                <h2>Questions?</h2>
                <p>
                    If you have questions about how WordleGroup works or the rules applied when keeping track of scores, visit the <a href="{{ route('rules-and-faq') }}">Rules and FAQ page</a>.
                </p>
                <p>
                    For technical issues or feedback, contact <a href="mailto:erik@pequod.sh">erik@pequod.sh</a>.
                </p>
            </div>
        </div>

        {{-- Quick Links --}}
        <x-layout.quick-links />
    </div>
</x-layout.page-container>
