<div>
    @if($scores->isNotEmpty())
        <ul role="list" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($scores as $score)
                <x-score.card :score="$score" :show-recorded-by="$showWhenRecordedByOtherUser"/>
            @endforeach
        </ul>
        <div class="mt-6">
            {{ $scores->links() }}
        </div>
    @else
        <p class="text-sm text-center text-zinc-500">You have not recorded any scores. Get started using the above
            form.</p>
    @endif
</div>
