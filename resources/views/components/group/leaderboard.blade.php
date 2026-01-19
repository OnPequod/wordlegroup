<div class="bg-white rounded-xl border border-zinc-200/70 shadow-sm overflow-hidden">
    <table class="min-w-full">
        <thead>
            <tr class="border-b border-zinc-100">
                <th scope="col" class="py-3 pl-4 pr-2 w-16 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Place</th>
                <th scope="col" class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Name</th>
                <th scope="col" class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500 whitespace-nowrap">Avg. Score</th>
                <th scope="col" class="py-3 pl-3 pr-4 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">Games</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-100">
            @foreach($leaderboard->leaderboard as $position)
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
                    <td class="px-3 py-3.5 whitespace-nowrap {{ $position['place'] <= 3 ? 'font-semibold text-zinc-900' : 'font-medium text-zinc-700' }}" title="{{ $position['name'] }}">
                        @if($anonymizePrivateUsers && $position['user'] && $position['user']->private_profile)
                            Anonymous User
                        @else
                            {{ $position['name'] }}
                        @endif
                    </td>
                    <td class="px-3 py-3.5 whitespace-nowrap text-right tabular-nums {{ $position['place'] <= 3 ? 'font-semibold text-zinc-900' : 'font-medium text-zinc-700' }}">
                        {!! $position['stats']['mean'] ?: "&#x2014;" !!}
                    </td>
                    <td class="py-3.5 pl-3 pr-4 whitespace-nowrap text-right tabular-nums {{ $position['place'] <= 3 ? 'font-semibold text-zinc-900' : 'font-medium text-zinc-700' }}">
                        {{ $position['stats']['count'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
