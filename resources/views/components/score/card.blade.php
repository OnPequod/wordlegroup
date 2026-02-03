@props([
    'score',
    'showUser' => false,
    'showRecordedBy' => false,
    'anonymizePrivateUsers' => false,
    'confirm' => true,
])

<li class="bg-white rounded-xl border border-zinc-200/70 shadow-sm shadow-zinc-900/5 overflow-hidden flex flex-col items-stretch">
    <a href="{{ route('score.share-page', $score) }}" class="block w-full">
        {{-- Header: board number, date, score --}}
        <div class="flex w-full items-center border-b border-zinc-100 px-4 py-3">
            <div class="font-semibold text-zinc-900">#{{ $score->board_number }}</div>
            <div class="flex-1 text-sm text-zinc-500 text-center">{{ $score->date->format('M j, Y') }}</div>
            <div class="font-semibold text-zinc-900">{{ $score->score === 7 ? 'X' : $score->score }}/6{{ $score->hard_mode ? '*' : '' }}</div>
        </div>
        {{-- Board --}}
        <div class="flex justify-center items-center py-6 min-h-44">
            @if($score->board)
                <x-score.board :score="$score"/>
            @else
                <div class="text-sm text-zinc-500">No board recorded.</div>
            @endif
        </div>
    </a>

    {{-- User info and metadata --}}
    @if($showUser)
        <div class="px-5 py-3 border-t border-zinc-100">
            <p class="text-sm text-zinc-700">
                <span class="font-medium">
                    @if($anonymizePrivateUsers && !$score->user->public_profile)
                        Anonymous
                    @else
                        {{ $score->user->name }}
                    @endif
                </span>
                <span class="text-zinc-400 mx-1">&middot;</span>
                <span class="text-zinc-500">{{ $score->created_at->diffForHumans() }}</span>
            </p>
            @if($score->comments_count > 0 || $score->hasBotScore())
                <div class="flex items-center gap-3 mt-1">
                    @if($score->comments_count > 0)
                        <span class="text-xs text-zinc-500">{{ $score->comments_count }} {{ Str::plural('comment', $score->comments_count) }}</span>
                    @endif
                    @if($score->hasBotScore())
                        @if($score->comments_count > 0)
                            <span class="text-xs text-zinc-400">&middot;</span>
                        @endif
                        @if($score->bot_skill_score !== null)
                            <span class="text-xs text-zinc-500">Skill {{ $score->bot_skill_score }}/99</span>
                        @endif
                        @if($score->bot_luck_score !== null)
                            <span class="text-xs text-zinc-500">Luck {{ $score->bot_luck_score }}/99</span>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    @elseif($showRecordedBy && !$score->recordedByUser())
        <div class="px-5 py-2 text-xs text-zinc-400 border-t border-zinc-100">Recorded by {{ $score->recordingUser->name }}</div>
    @endif

    {{-- Share buttons --}}
    <div class="px-5 py-3 border-t border-zinc-100">
        <livewire:score.share :icon-size="3" :score="$score" :key="'card-'.$score->id" :confirm="$confirm"/>
    </div>
</li>
