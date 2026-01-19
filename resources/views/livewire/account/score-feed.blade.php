<div>
    @if($scores->isNotEmpty())
        <ul
            role="list" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"
        >
            @foreach($scores as $score)
                <li class="bg-white rounded-xl border border-zinc-200/70 shadow-sm shadow-zinc-900/5 overflow-hidden flex flex-col items-stretch">
                    <a href="{{ route('score.share-page', $score) }}" class="block w-full">
                        <x-score.display :score="$score"/>
                    </a>
                    @if($showWhenRecordedByOtherUser && ! $score->recordedByUser())
                        <div class="px-5 py-2 text-xs text-zinc-400 border-t border-zinc-100">Recorded by {{ $score->recordingUser->name }}</div>
                    @endif
                    <div class="px-5 py-3 border-t border-zinc-100">
                        <livewire:score.share :icon-size="3" :score="$score" :key="$score->id" :confirm="true"/>
                    </div>
                </li>
            @endforeach
        </ul>
        <div class="flex justify-center mt-6">
            {{ $scores->links() }}
        </div>
    @else
        <p class="text-sm text-center text-zinc-500">You have not recorded any scores. Get started using the above
            form.</p>
    @endif
</div>
