<div>
    {{-- Header --}}
    <div class="mb-4">
        <h3 class="text-sm font-medium text-zinc-700">Group Stats</h3>
        <p class="text-sm text-zinc-500">{{ $leaderboard->scores_recorded }} {{ Str::plural('score', $leaderboard->scores_recorded) }} recorded</p>
    </div>

    {{-- KPI Row: Segmented strip --}}
    <div class="rounded-xl bg-zinc-50 border border-zinc-100 overflow-hidden">
        <dl class="grid grid-cols-3 divide-x divide-zinc-100">
            <div class="px-3 py-3 text-center">
                <dd class="text-2xl font-semibold text-zinc-900 tabular-nums">{{ $leaderboard->score_median }}</dd>
                <dt class="mt-0.5 text-xs font-medium text-zinc-500">Median</dt>
            </div>
            <div class="px-3 py-3 text-center">
                <dd class="text-2xl font-semibold text-zinc-900 tabular-nums">{{ $leaderboard->score_mean }}</dd>
                <dt class="mt-0.5 text-xs font-medium text-zinc-500">Mean</dt>
            </div>
            <div class="px-3 py-3 text-center">
                <dd class="text-2xl font-semibold text-zinc-900 tabular-nums">{{ $leaderboard->score_mode }}</dd>
                <dt class="mt-0.5 text-xs font-medium text-zinc-500">Mode</dt>
            </div>
        </dl>
    </div>

    {{-- Chart --}}
    <div class="mt-4 rounded-xl bg-zinc-50/50 border border-zinc-100 p-3">
        <div class="text-xs font-medium text-zinc-500 mb-2">Score Distribution</div>
        <x-score.bar-chart :score-distribution="$leaderboard->score_distribution" />
    </div>
</div>
