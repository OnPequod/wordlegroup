<div>
    <dl class="grid grid-cols-1 divide-y divide-zinc-200 sm:grid-cols-3 sm:divide-y-0 sm:divide-x">
        <div class="py-2 text-center sm:px-4">
            <dt class="text-sm text-zinc-500">Median</dt>
            <dd class="mt-1 text-3xl font-semibold tabular-nums text-zinc-900">{{ $user->daily_score_median }}</dd>
        </div>
        <div class="py-2 text-center sm:px-4">
            <dt class="text-sm text-zinc-500">Mean</dt>
            <dd class="mt-1 text-3xl font-semibold tabular-nums text-zinc-900">{{ $user->daily_score_mean }}</dd>
        </div>
        <div class="py-2 text-center sm:px-4">
            <dt class="text-sm text-zinc-500">Mode</dt>
            <dd class="mt-1 text-3xl font-semibold tabular-nums text-zinc-900">{{ $user->daily_score_mode }}</dd>
        </div>
    </dl>

    <div class="my-6 border-t border-zinc-200/70"></div>

    <div>
        <div class="text-base font-semibold text-zinc-900">Distribution</div>
        <div class="mt-4">
            <div class="mx-auto max-w-4xl">
                <x-score.bar-chart :score-distribution="$user->score_distribution" />
            </div>
        </div>
    </div>

    <div class="mt-4 text-sm text-zinc-500">
        <span class="font-semibold text-zinc-700">{{ $user->daily_scores_recorded }}</span>
        recorded {{ Str::plural('score', $user->daily_scores_recorded ) }}.
    </div>
</div>
