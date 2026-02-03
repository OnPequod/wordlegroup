<x-layout.page-container title="Wordle Group Account Settings" :wide="true" :top-padding="false">

    <x-account.home-layout page="settings">

        <form wire:submit.prevent="update" class="mb-0">
            <div class="flex flex-col gap-8 pb-12">
                {{-- Header --}}
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-zinc-900 font-serif">Settings</h1>
                        <p class="mt-1 text-sm text-zinc-500">Manage your account preferences</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <x-form.input.button
                            type="submit"
                            loading-action="update"
                            class="w-36"
                            :primary="true"
                        >
                            Save Changes
                        </x-form.input.button>
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
                            @if($confirmEmailChange)
                                <div class="mt-3 p-4 rounded-lg bg-amber-50 border border-amber-200">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-semibold text-amber-800">Confirm Email Change</h4>
                                            <p class="mt-1 text-sm text-amber-700">Your email address is used to log in. Changing it will update your login credentials. Click "Save Changes" again to confirm.</p>
                                            <button
                                                type="button"
                                                wire:click="cancelEmailChange"
                                                class="mt-2 text-sm font-medium text-amber-800 hover:text-amber-900 underline"
                                            >
                                                Cancel and keep current email
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Public Leaderboard --}}
                    <div class="rounded-2xl bg-white border border-zinc-200 shadow-sm p-6 md:p-8">
                        <div class="pb-3 border-b border-zinc-100">
                            <h3 class="text-lg font-semibold text-zinc-900">Public Leaderboard</h3>
                            <p class="text-sm text-zinc-500 mt-1">Compete with Wordle players worldwide</p>
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
                                    <p class="mt-1 text-sm text-zinc-600 leading-snug">Your scores will be included in the <a href="{{ route('leaderboard') }}" class="text-green-700 hover:underline">public leaderboard</a>. By default, you will appear as "Anonymous".</p>
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

                    {{-- Export Data --}}
                    <div class="rounded-2xl bg-white border border-zinc-200 shadow-sm p-6 md:p-8">
                        <h3 class="text-lg font-semibold text-zinc-900 pb-3 border-b border-zinc-100">Export Data</h3>
                        <div class="pt-5">
                            <p class="text-sm text-zinc-600 mb-4">Download all your Wordle scores as a CSV file.</p>
                            <a
                                href="{{ route('account.export.scores.csv') }}"
                                class="inline-flex items-center gap-2 rounded-md border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Download CSV
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </form>

    </x-account.home-layout>

</x-layout.page-container>
