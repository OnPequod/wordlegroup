<x-layout.page-container
    :heading="$title"
    :title="$title . ' - Wordle Group'"
>

    <x-layout.social-meta
        :title="$title . ' - Wordle Group'"
        :url="route('score.share-page', $score)"
        description="{{ $score->boardShareText }}"
    />

    <div class="-mt-4 text-gray-500 text-base font-bold text-center ">
        by {{ $score->user->name }}
    </div>

    <div class="mt-8 mb-10 text-4xl text-green-700 text-center font-bold">
        {{ $score->scoreDisplay }}/6{{ $score->hard_mode ? '*' : '' }}
    </div>

    @if($boardVisible)
        <div
            class="flex justify-center text-4xl"
        >
            <x-score.board :score="$score"/>
        </div>
    @else
        <div class="w-72 mx-auto">
            <div class="text-center text-gray-500 text-sm">
                <div
                    class="flex flex-col items-center justify-center text-4xl"
                >
                    <x-score.hidden-board :score="$score"/>
                </div>
                <p class="mt-8">
                    This board is for today's Wordle.
                </p>
                <p class="mt-4">
                    Because you share a group with {{ $score->user->name  }}, you must first <a
                        class="link" href="{{ route('account.record-score') }}"
                    >record your board for today</a> to see it.
                </p>
                <p class="mt-4">You will be able to see this board tomorrow.</p>
            </div>
        </div>
    @endif

    @if($beingViewedByOwner)
        <x-layout.hr class="mt-12 mb-8" />
        <div class="mx-auto w-full max-w-sm flex flex-col items-center justify-center">
            <x-layout.sub-heading>Share</x-layout.sub-heading>
            <div class="mt-4 flex items-center">
                <livewire:score.share
                    :score="$score"
                    :show-copy-button="true"
                    :show-copy-icon="true"
                    :group-copy-with-all-buttons="true"
                    button-class="w-10 h-10 inline-flex items-center justify-center bg-green-700 hover:bg-wordle-yellow rounded-full text-white hover:text-green-50"
                />
            </div>
        </div>
    @elseif($score->user->public_profile || $score->public)
        <x-layout.hr class="mt-12 mb-8" />
        <div class="mx-auto w-full max-w-sm flex flex-col items-center justify-center">
            <x-layout.sub-heading>Share</x-layout.sub-heading>
            <div class="mt-4 flex items-center">
                <livewire:score.share
                    :score="$score"
                    :show-copy-button="true"
                    :show-copy-icon="true"
                    :group-copy-with-all-buttons="true"
                    :confirm="false"
                    button-class="w-10 h-10 inline-flex items-center justify-center bg-green-700 hover:bg-wordle-yellow rounded-full text-white hover:text-green-50"
                />
            </div>
        </div>
    @endif

    <x-layout.hr class="mt-12 mb-8" />

    <div class="mt-12 text-sm text-gray-500 text-center">
        <span class="font-medium">
            Wordle {{ $score->board_number }}
        </span>
        is from
        <span class="font-medium">
            {{ $score->date->format('l, F d, Y') }}.
        </span>
    </div>

    @if($score->hasBotScore())
        <x-layout.hr class="mt-8 mb-8" />
        <div class="flex justify-center gap-8 text-center">
            @if($score->bot_skill_score !== null)
                <div>
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Bot Skill</div>
                    <div class="mt-1 text-xl font-bold text-green-700">{{ $score->botSkillDisplay }}</div>
                </div>
            @endif
            @if($score->bot_luck_score !== null)
                <div>
                    <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Bot Luck</div>
                    <div class="mt-1 text-xl font-bold text-green-700">{{ $score->botLuckDisplay }}</div>
                </div>
            @endif
        </div>
    @endif

    <x-layout.hr class="mt-8 mb-8" />

    <div class="max-w-xl mx-auto">
        <livewire:score.comments :score="$score" />
    </div>


    @if(! Auth::check())
        <x-layout.hr class="my-16"/>

        <div class="text-center">
            <a class="link" href="{{ route('home') }}">Create Your Own Wordle Group</a>
        </div>
    @endif
</x-layout.page-container>
