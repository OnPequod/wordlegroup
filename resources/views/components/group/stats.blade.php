<div class="rounded-2xl bg-white border border-zinc-200 shadow-sm p-6">
    {{-- Header --}}
    <div class="mb-5">
        <h3 class="text-lg font-semibold text-zinc-900">Group Stats</h3>
        <p class="text-sm text-zinc-500">{{ $leaderboard->scores_recorded }} {{ Str::plural('score', $leaderboard->scores_recorded) }} recorded</p>
    </div>

    {{-- KPI Row: Segmented strip --}}
    <div class="rounded-xl bg-zinc-50 border border-zinc-200 overflow-hidden">
        <dl class="grid grid-cols-3 divide-x divide-zinc-200">
            <div class="px-4 py-4 text-center">
                <dd class="text-3xl font-semibold text-zinc-900 tabular-nums">{{ $leaderboard->score_median }}</dd>
                <dt class="mt-1 text-sm font-medium text-zinc-500">Median</dt>
            </div>
            <div class="px-4 py-4 text-center">
                <dd class="text-3xl font-semibold text-zinc-900 tabular-nums">{{ $leaderboard->score_mean }}</dd>
                <dt class="mt-1 text-sm font-medium text-zinc-500">Mean</dt>
            </div>
            <div class="px-4 py-4 text-center">
                <dd class="text-3xl font-semibold text-zinc-900 tabular-nums">{{ $leaderboard->score_mode }}</dd>
                <dt class="mt-1 text-sm font-medium text-zinc-500">Mode</dt>
            </div>
        </dl>
    </div>

    {{-- Chart --}}
    <div class="mt-5 rounded-xl bg-zinc-50/50 border border-zinc-200 p-4">
        <div class="text-xs font-medium text-zinc-500 mb-3">Score Distribution</div>
        <x-score.bar-chart :score-distribution="$leaderboard->score_distribution" />
    </div>
</div>
