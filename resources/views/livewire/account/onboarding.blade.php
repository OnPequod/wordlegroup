<x-layout.page-container title="What's New - Wordle Group" :wide="true" :top-padding="false">

    <form wire:submit.prevent="complete" class="mb-0">
        <div class="flex flex-col gap-8 pb-12">
            {{-- Header --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 font-serif">What's New in Wordle Group</h1>
                    <p class="mt-1 text-sm text-zinc-500">We've added some new features. Review your settings below.</p>
                </div>
                <div class="flex flex-wrap items-center gap-6">
                    <button
                        type="button"
                        wire:click="dismiss"
                        class="text-sm text-zinc-500 hover:text-zinc-700 transition"
                    >
                        Dismiss
                    </button>
                    <x-form.input.button
                        type="submit"
                        loading-action="complete"
                        class="whitespace-nowrap"
                        :primary="true"
                    >
                        Save & Continue
                    </x-form.input.button>
                </div>
            </div>

            {{-- What's New Section - Full Width --}}
            <div class="rounded-2xl bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 shadow-sm p-6 md:p-8">
                <h3 class="text-lg font-semibold text-green-900 pb-3 border-b border-green-200">New Features</h3>
                <div class="pt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-12 gap-y-8">
                    <a href="{{ route('leaderboard') }}" class="flex gap-4 group">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center text-sm font-bold">1</div>
                        <div>
                            <span class="font-semibold text-zinc-900 group-hover:text-green-700 transition">Public Leaderboard</span>
                            <p class="text-sm text-zinc-600">Compete globally and see where you rank among all players.</p>
                        </div>
                    </a>
                    <a href="{{ route('board.archive') }}" class="flex gap-4 group">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center text-sm font-bold">2</div>
                        <div>
                            <span class="font-semibold text-zinc-900 group-hover:text-green-700 transition">Puzzle Archive</span>
                            <p class="text-sm text-zinc-600">Browse past puzzles, answers, and daily statistics.</p>
                        </div>
                    </a>
                    <a href="{{ route('board') }}" class="flex gap-4 group">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center text-sm font-bold">3</div>
                        <div>
                            <span class="font-semibold text-zinc-900 group-hover:text-green-700 transition">Daily Puzzle Report</span>
                            <p class="text-sm text-zinc-600">See today's answer, score distribution, and public discussion.</p>
                        </div>
                    </a>
                    <a href="{{ route('account.home') }}" class="flex gap-4 group">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center text-sm font-bold">4</div>
                        <div>
                            <span class="font-semibold text-zinc-900 group-hover:text-green-700 transition">Group Discussions</span>
                            <p class="text-sm text-zinc-600">Chat with your group members on each group's page.</p>
                        </div>
                    </a>
                    <a href="{{ route('board') }}" class="flex gap-4 group">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center text-sm font-bold">5</div>
                        <div>
                            <span class="font-semibold text-zinc-900 group-hover:text-green-700 transition">Skill & Luck Scores</span>
                            <p class="text-sm text-zinc-600">See skill and luck metrics powered by the NYT WordleBot.</p>
                        </div>
                    </a>
                    <a href="{{ route('account.home') }}" class="flex gap-4 group">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center text-sm font-bold">6</div>
                        <div>
                            <span class="font-semibold text-zinc-900 group-hover:text-green-700 transition">Export Scores</span>
                            <p class="text-sm text-zinc-600">Download your complete score history as a CSV file.</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                {{-- Profile --}}
                <div class="rounded-2xl bg-white border border-zinc-200 shadow-sm p-6 md:p-8">
                    <h3 class="text-lg font-semibold text-zinc-900 pb-3 border-b border-zinc-100">Profile</h3>
                    <div class="pt-5 space-y-5">
                        <x-form.input.text
                            :errors="$errors"
                            name="name"
                            label="Name"
                            wire:model="name"
                            placeholder="Your name"
                        />
                        <x-form.input.text
                            :errors="$errors"
                            name="publicAlias"
                            label="Public Alias (optional)"
                            wire:model="publicAlias"
                            placeholder="Your public display name"
                        />
                        <p class="text-xs text-zinc-500 -mt-3">If set, this name will be shown on public leaderboards instead of your real name.</p>
                        <x-form.input.text
                            :errors="$errors"
                            name="email"
                            label="Email"
                            type="email"
                            wire:model="email"
                            placeholder="your@email.com"
                        />
                    </div>
                </div>

                {{-- Public Leaderboard --}}
                <div class="rounded-2xl bg-white border border-zinc-200 shadow-sm p-6 md:p-8 overflow-hidden">
                    <div class="flex items-start justify-between pb-3 border-b border-zinc-100">
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-900">Public Leaderboard</h3>
                            <p class="text-sm text-zinc-500 mt-1">Compete with Wordle players worldwide</p>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">New</span>
                    </div>
                    <div class="divide-y divide-zinc-100">
                        <label
                            for="showOnPublicLeaderboard"
                            class="flex items-start gap-3 py-5 px-3 -mx-3 rounded-lg cursor-pointer hover:bg-zinc-50/50 transition"
                        >
                            <input
                                type="checkbox"
                                id="showOnPublicLeaderboard"
                                wire:model.live="showOnPublicLeaderboard"
                                class="mt-1 h-4 w-4 rounded border-zinc-300 text-green-700 focus:ring-green-600"
                            >
                            <div class="flex-1">
                                <span class="text-sm font-medium text-zinc-900">Participate in the public leaderboard</span>
                                <p class="mt-1 text-sm text-zinc-600 leading-snug">Your scores will be included in the public leaderboard. By default, you will appear as "Anonymous".</p>
                            </div>
                        </label>

                        <label
                            for="showNameOnPublicLeaderboard"
                            class="flex items-start gap-3 py-5 px-3 -mx-3 rounded-lg cursor-pointer hover:bg-zinc-50/50 transition {{ !$showOnPublicLeaderboard ? 'opacity-50' : '' }}"
                        >
                            <input
                                type="checkbox"
                                id="showNameOnPublicLeaderboard"
                                wire:model="showNameOnPublicLeaderboard"
                                {{ !$showOnPublicLeaderboard ? 'disabled' : '' }}
                                class="mt-1 h-4 w-4 rounded border-zinc-300 text-green-700 focus:ring-green-600"
                            >
                            <div class="flex-1">
                                <span class="text-sm font-medium text-zinc-900">Display my name publicly</span>
                                <p class="mt-1 text-sm text-zinc-600 leading-snug">Show your public alias (if set) or your account name instead of "Anonymous".</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Privacy --}}
                <div class="rounded-2xl bg-white border border-zinc-200 shadow-sm p-6 md:p-8">
                    <h3 class="text-lg font-semibold text-zinc-900 pb-3 border-b border-zinc-100">Privacy</h3>
                    <div class="divide-y divide-zinc-100">
                        <label
                            for="publicProfile"
                            class="flex items-start gap-3 py-5 px-3 -mx-3 rounded-lg cursor-pointer hover:bg-zinc-50/50 transition"
                        >
                            <input
                                type="checkbox"
                                id="publicProfile"
                                wire:model="publicProfile"
                                class="mt-1 h-4 w-4 rounded border-zinc-300 text-green-700 focus:ring-green-600"
                            >
                            <div class="flex-1">
                                <span class="text-sm font-medium text-zinc-900">Make my profile public</span>
                                <p class="mt-1 text-sm text-zinc-600 leading-snug">Share your stats with anyone, even people who aren't in your Wordle Groups.</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Email Preferences --}}
                <div class="rounded-2xl bg-white border border-zinc-200 shadow-sm p-6 md:p-8">
                    <h3 class="text-lg font-semibold text-zinc-900 pb-3 border-b border-zinc-100">Email Preferences</h3>
                    <div class="divide-y divide-zinc-100">
                        <label
                            for="allowReminderEmails"
                            class="flex items-start gap-3 py-5 px-3 -mx-3 rounded-lg cursor-pointer hover:bg-zinc-50/50 transition"
                        >
                            <input
                                type="checkbox"
                                id="allowReminderEmails"
                                wire:model="allowReminderEmails"
                                class="mt-1 h-4 w-4 rounded border-zinc-300 text-green-700 focus:ring-green-600"
                            >
                            <div class="flex-1">
                                <span class="text-sm font-medium text-zinc-900">Reminder emails</span>
                                <p class="mt-1 text-sm text-zinc-600 leading-snug">Let others in your group remind you to record your score. You'll never get more than one reminder per day.</p>
                            </div>
                        </label>
                    </div>
                </div>

            </div>
        </div>
    </form>

</x-layout.page-container>
