<div>
<x-layout.page-container title="Wordle Group Account" :wide="true" :top-padding="false">

    <x-account.home-layout page="home">

        <div class="flex flex-col gap-8 pb-12">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-zinc-900">My Stats</h1>
                    <p class="mt-1 text-sm text-zinc-500">{{ $user->daily_scores_recorded }} recorded scores</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    @if($user->public_profile)
                        <a
                            class="rounded-md border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50"
                            href="#share"
                        >Share</a>
                    @endif
                    <a
                        class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800"
                        href="{{ route('account.record-score') }}"
                    >Record score</a>
                </div>
            </div>

            @if($user->pendingGroupInvitations->isNotEmpty())
                <div class="bg-white rounded-xl border border-zinc-200/70 shadow-sm shadow-zinc-900/5 p-8">
                    <x-layout.sub-heading>Pending Group Invitations</x-layout.sub-heading>
                    <div class="mt-4">
                        <livewire:account.pending-group-invitations :user="$user"/>
                    </div>
                </div>
            @endif

            @if($user->daily_scores_recorded > 0)
                <div class="bg-white rounded-xl border border-zinc-200/70 shadow-sm shadow-zinc-900/5 p-8">
                    <x-layout.sub-heading>Stats Summary</x-layout.sub-heading>
                    <p class="mt-1 text-sm text-zinc-500">Median, mean, mode, and distribution.</p>
                    <div class="mt-4">
                        <x-account.stats :user="$user"/>
                    </div>
                </div>
            @endif

            @unless($user->dismissed_email_notification)
                <div class="bg-white rounded-xl border border-zinc-200/70 shadow-sm shadow-zinc-900/5 p-8">
                    <x-layout.sub-heading>Email Your Scores</x-layout.sub-heading>
                    <x-score.email-prompt class="mt-2 text-sm text-zinc-500"/>
                    <div class="mt-4">
                        <livewire:score.dismiss-email-prompt-notification
                            :user="$user"
                            class="text-xs text-zinc-500 hover:text-zinc-700"
                        />
                    </div>
                </div>
            @endunless

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <div class="bg-white rounded-xl border border-zinc-200/70 shadow-sm shadow-zinc-900/5 p-8">
                    <x-layout.sub-heading>My Groups</x-layout.sub-heading>
                    <div class="mt-6">
                        <x-account.groups-list :user="$user"/>
                    </div>
                    <div class="mt-6">
                        <a
                            class="inline-flex items-center rounded-md border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50"
                            href="{{ route('group.create') }}"
                        >
                            <x-icon-regular.plus class="mr-2 h-4 w-4 fill-zinc-500"/>
                            Create a new group</a>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-zinc-200/70 shadow-sm shadow-zinc-900/5 p-8">
                    <x-layout.sub-heading>Record a Score</x-layout.sub-heading>
                    <p class="mt-1 text-sm text-zinc-500">Paste your Wordle board to save it.</p>
                    @if($user->daily_scores_recorded === 0)
                        <div class="mt-3 text-sm text-zinc-500">
                            <p>You have not yet recorded any scores.</p>
                            <p class="mt-2">
                                Don't have your board? <a
                                    class="link" href="{{ route('account.record-score') }}"
                                >Enter your score manually.</a>
                            </p>
                        </div>
                    @endif
                    <div class="mt-5">
                        <livewire:score.record-form :quick="true" :user="$user" :hide-email="true"/>
                        @if($user->dismissed_email_notification)
                            <div class="mt-5 text-center text-xs text-zinc-500">
                                <x-account.email-scores-message/>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($user->public_profile)
                <div id="share" class="flex justify-center">
                    <x-account.share-links :user="$user" />
                </div>
            @endif

            <div>
                <x-layout.hr class="mb-8" />
                <h2 class="text-xl font-semibold text-zinc-900 text-center">My Scores</h2>
                <p class="mt-1 text-sm text-zinc-500 text-center">Recent scores you have recorded.</p>
                <div class="mt-6">
                    <livewire:account.score-feed :user="$user" :show-when-recorded-by-other-user="true"/>
                </div>
            </div>
        </div>
    </x-account.home-layout>

</x-layout.page-container>
</div>
