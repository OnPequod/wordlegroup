@props([
    'score',
    'showUser' => false,
    'showRecordedBy' => false,
    'anonymizePrivateUsers' => false,
    'confirm' => true,
    'flat' => false,
])

<li @class([
    'flex flex-col items-stretch overflow-hidden',
    'bg-white rounded-xl border border-zinc-200/70 shadow-sm shadow-zinc-900/5' => !$flat,
    'border-b border-zinc-100 lg:odd:border-r' => $flat,
])>
    <a href="{{ route('score.share-page', $score) }}" class="block w-full">
        {{-- Header: user name, score, board number --}}
        <div class="flex w-full items-center justify-center gap-4 px-4 pt-6 pb-2">
            @if($showUser)
                @if($anonymizePrivateUsers && !$score->user->public_profile)
                    <span class="font-bold text-zinc-900 whitespace-nowrap">Anonymous</span>
                @else
                    <span class="font-bold text-zinc-900 whitespace-nowrap">{{ $score->user->name }}</span>
                @endif
            @endif
            <span class="font-medium text-zinc-700 tabular-nums">{{ $score->score === 7 ? 'X' : $score->score }}/6{{ $score->hard_mode ? '*' : '' }}</span>
            <span class="text-sm text-zinc-400 tabular-nums">#{{ $score->board_number }}</span>
        </div>

        {{-- Board --}}
        <div class="flex justify-center items-center py-2 min-h-28">
            @if($score->board)
                <x-score.board :score="$score"/>
            @else
                <div class="text-sm text-zinc-500">No board recorded.</div>
            @endif
        </div>

        {{-- Skill/Luck scores --}}
        @if($score->hasBotScore())
            <div class="text-center text-xs text-zinc-400 pt-1">
                @if($score->bot_skill_score !== null)
                    <span>Skill {{ $score->bot_skill_score }}</span>
                @endif
                @if($score->bot_skill_score !== null && $score->bot_luck_score !== null)
                    <span class="mx-1 opacity-50">·</span>
                @endif
                @if($score->bot_luck_score !== null)
                    <span>Luck {{ $score->bot_luck_score }}</span>
                @endif
            </div>
        @endif

        {{-- Date centered below board --}}
        <div class="text-center text-xs text-zinc-400 pt-2 pb-6">
            {{ $score->date->format('M j, Y') }}
        </div>
    </a>

    @unless($flat)
        {{-- User info and metadata (non-flat only) --}}
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

        {{-- Share buttons (non-flat only) --}}
        <div class="px-5 py-3 border-t border-zinc-100">
            <livewire:score.share :icon-size="3" :score="$score" :key="'card-'.$score->id" :confirm="$confirm"/>
        </div>
    @endunless
</li>
