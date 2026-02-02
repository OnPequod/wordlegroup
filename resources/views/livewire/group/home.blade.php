<x-layout.page-container
    :title="$group->name . ' Wordle Group'"
    :wide="true"
    :top-padding="false"
>

    <x-layout.social-meta
        title="{{ $group->name }} - Wordle Group Leaderboard & Stats"
        :url="route('group.home', $group)"
        description="Wordle Group is a way to keep score with a group of friends and track your Wordle performance over time."
    />

    <x-account.home-layout :page="'group.' . $group->id">

        <div class="flex flex-col gap-8 pb-12">
            {{-- Header --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 font-serif">{{ $group->name }}</h1>
                    <p class="mt-1 text-sm text-zinc-500">
                        {{ $memberCount }} {{ Str::plural('member', $memberCount) }} · {{ $group->scores_recorded }} scores recorded
                        @if($isAdmin)
                            · <x-group.admin-badge />
                        @endif
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    @if($isAdmin)
                        <a
                            class="rounded-md border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50"
                            href="{{ route('group.settings', $group) }}"
                        >Settings</a>
                    @endif
                    @if($memberOfGroup)
                        <a
                            class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800"
                            href="{{ route('account.record-score') }}"
                        >Record score</a>
                    @endif
                </div>
            </div>

            {{-- Pending Invitations (Admin only) --}}
            @if($isAdmin && $group->pendingInvitations->isNotEmpty())
                <div class="bg-white rounded-xl border border-zinc-200/70 shadow-sm shadow-zinc-900/5 p-8">
                    <x-layout.sub-heading>Pending Group Invitations</x-layout.sub-heading>
                    <div class="mt-4">
                        <livewire:group.pending-invitations :group="$group"/>
                    </div>
                </div>
            @endif

            {{-- Invite prompt for solo groups --}}
            @if($memberOfGroup && $memberCount === 1)
                <livewire:group.invite-member :group="$group"/>
            @endif

            {{-- Main Two-Column Grid: Leaderboard + Activity/Discussion --}}
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                {{-- Left Column: Leaderboard/Stats --}}
                <div class="flex flex-col gap-8">
                    @if($group->scores_recorded > 0)
                        <x-group.statistics-tabs
                            :group="$group"
                            :member-of-group="$memberOfGroup"
                            :anonymize-private-users="$group->public && !$memberOfGroup"
                        />
                    @else
                        <div class="bg-white rounded-xl border border-zinc-200/70 shadow-sm shadow-zinc-900/5 p-8">
                            <p class="text-zinc-500 text-sm text-center">No one has recorded any scores yet. Invite some users below!</p>
                        </div>
                    @endif

                    @if($memberOfGroup)
                        {{-- Record a Score --}}
                        <div class="bg-white rounded-xl border border-zinc-200/70 shadow-sm shadow-zinc-900/5 p-8">
                            <x-layout.sub-heading>Record a Score</x-layout.sub-heading>
                            <p class="mt-1 text-sm text-zinc-500">Paste your Wordle board to save it.</p>
                            <div class="mt-5">
                                <livewire:score.record-form :quick="true" :user="$user" :group="$group" :hide-email="true"/>
                            </div>
                            @if($user->dismissed_email_notification)
                                <div class="mt-5 text-center text-xs text-zinc-500">
                                    <x-account.email-scores-message/>
                                </div>
                            @endif
                        </div>

                        {{-- Invite Members --}}
                        @if($memberCount > 1)
                            <livewire:group.invite-member :group="$group"/>
                        @endif

                        {{-- Member List --}}
                        <livewire:group.member-list :group="$group"/>

                        {{-- Email notification (if not dismissed) --}}
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

                        {{-- Share Links (for public groups) --}}
                        @if($group->public)
                            <div class="bg-white rounded-xl border border-zinc-200/70 shadow-sm shadow-zinc-900/5 p-8">
                                <x-layout.sub-heading>Share This Group</x-layout.sub-heading>
                                <p class="mt-1 text-sm text-zinc-500">Invite others to join your group.</p>
                                <x-group.share-links :group="$group" class="mt-4" />
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Right Column: Social Feed (Tabbed Activity + Discussion) --}}
                <div class="flex flex-col gap-8">
                    @if($memberOfGroup || $group->public)
                        <livewire:group.social-feed
                            :group="$group"
                            :anonymize-private-users="$group->public && !$memberOfGroup"
                        />
                    @endif
                </div>
            </div>
        </div>

    </x-account.home-layout>

</x-layout.page-container>
