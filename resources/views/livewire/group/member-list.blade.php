<div class="rounded-2xl bg-white border border-zinc-200 shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="flex items-center justify-between px-6 py-5 border-b border-zinc-100">
        <div>
            <h3 class="text-lg font-semibold text-zinc-900">Group Members</h3>
            <p class="text-sm text-zinc-500 mt-0.5">{{ $group->memberships->count() }} {{ Str::plural('member', $group->memberships->count()) }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-zinc-50/50 sticky top-0">
                <tr>
                    <th scope="col" class="py-3 pl-6 pr-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">
                        Name
                    </th>
                    <th scope="col" class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">
                        Games
                    </th>
                    <th scope="col" class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">
                        Mean
                    </th>
                    <th scope="col" class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">
                        Median
                    </th>
                    <th scope="col" class="py-3 pl-3 pr-6 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">
                        Mode
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @foreach($group->memberships as $membership)
                    <tr class="hover:bg-zinc-50/50 transition">
                        <td class="py-4 pl-6 pr-3">
                            <div class="flex items-center gap-3">
                                {{-- Avatar --}}
                                <div class="flex-shrink-0 h-9 w-9 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center font-semibold text-sm">
                                    {{ substr($membership->user->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-zinc-900 text-sm">{{ $membership->user->name }}</span>
                                        @if(Auth::id() === $membership->user->id)
                                            <span class="inline-flex items-center rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600">You</span>
                                        @endif
                                        @if($group->admin_id === $membership->user->id)
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Admin</span>
                                        @endif
                                    </div>
                                    @if($group->isAdmin(Auth::user()) && Auth::id() !== $membership->user->id)
                                        <div class="flex items-center gap-3 mt-1">
                                            @if($membership->user->canBeNudged())
                                                <button
                                                    type="button"
                                                    title="Send a reminder to record scores"
                                                    class="text-xs text-zinc-500 hover:text-green-700 transition"
                                                    onclick="confirm('Send {{ $membership->user->name }} a reminder to record their scores?') || event.stopImmediatePropagation()"
                                                    wire:click="nudge({{ $membership->user->id }})"
                                                >
                                                    Nudge
                                                </button>
                                            @endif
                                            <button
                                                type="button"
                                                title="Remove from group"
                                                class="text-xs text-zinc-500 hover:text-red-600 transition"
                                                onclick="confirm('Remove {{ $membership->user->name }} from this group?') || event.stopImmediatePropagation()"
                                                wire:click="remove({{ $membership->user->id }})"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-zinc-600 text-right tabular-nums">
                            {{ number_format($membership->user->daily_scores_recorded) }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-zinc-600 text-right tabular-nums">
                            {{ number_format($membership->user->daily_score_mean, 2) }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-zinc-600 text-right tabular-nums">
                            {{ number_format($membership->user->daily_score_median, 2) }}
                        </td>
                        <td class="whitespace-nowrap py-4 pl-3 pr-6 text-sm text-zinc-600 text-right tabular-nums">
                            {{ number_format($membership->user->daily_score_mode, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
