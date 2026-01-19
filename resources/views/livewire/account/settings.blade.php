<x-layout.page-container heading="Settings" title="Wordle Group Account Settings">

    <x-account.home-layout page="settings">

        <div class="max-w-2xl mx-auto">
            <div class="rounded-2xl bg-white border border-zinc-200 shadow-sm p-6 md:p-8">

                <form wire:submit.prevent="update" class="mb-0">

                    {{-- Section: Profile --}}
                    <div>
                        <h3 class="text-lg font-semibold text-zinc-900 pb-3 border-b border-zinc-100">Profile</h3>
                        <div class="pt-5">
                            <x-form.input.text
                                :errors="$errors"
                                name="user.name"
                                label="Name"
                                wire:model="user.name"
                                placeholder="Your name"
                            />
                        </div>
                    </div>

                    {{-- Section: Privacy --}}
                    <div class="mt-10">
                        <h3 class="text-lg font-semibold text-zinc-900 pb-3 border-b border-zinc-100">Privacy</h3>
                        <div class="divide-y divide-zinc-100">
                            <label
                                for="publicProfile"
                                class="flex items-start gap-3 py-5 px-3 -mx-3 rounded-lg cursor-pointer hover:bg-zinc-50/50 transition"
                            >
                                <input
                                    type="checkbox"
                                    id="publicProfile"
                                    name="publicProfile"
                                    wire:model="user.public_profile"
                                    class="mt-1 h-4 w-4 rounded border-zinc-300 text-green-700 focus:ring-green-600"
                                >
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-zinc-900">Make my profile public</span>
                                    <p class="mt-1 text-sm text-zinc-600 leading-snug">Share your stats with anyone, even people who aren't in your Wordle Groups.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Section: Email Preferences --}}
                    <div class="mt-10">
                        <h3 class="text-lg font-semibold text-zinc-900 pb-3 border-b border-zinc-100">Email Preferences</h3>
                        <div class="divide-y divide-zinc-100">
                            <label
                                for="allowDigestEmails"
                                class="flex items-start gap-3 py-5 px-3 -mx-3 rounded-lg cursor-pointer hover:bg-zinc-50/50 transition"
                            >
                                <input
                                    type="checkbox"
                                    id="allowDigestEmails"
                                    name="allowDigestEmails"
                                    wire:model="user.allow_digest_emails"
                                    class="mt-1 h-4 w-4 rounded border-zinc-300 text-green-700 focus:ring-green-600"
                                >
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-zinc-900">Weekly digest emails</span>
                                    <p class="mt-1 text-sm text-zinc-600 leading-snug">Receive a weekly report on your scores and how you're doing in your groups.</p>
                                </div>
                            </label>

                            <label
                                for="allowReminderEmails"
                                class="flex items-start gap-3 py-5 px-3 -mx-3 rounded-lg cursor-pointer hover:bg-zinc-50/50 transition"
                            >
                                <input
                                    type="checkbox"
                                    id="allowReminderEmails"
                                    name="allowReminderEmails"
                                    wire:model="user.allow_reminder_emails"
                                    class="mt-1 h-4 w-4 rounded border-zinc-300 text-green-700 focus:ring-green-600"
                                >
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-zinc-900">Reminder emails</span>
                                    <p class="mt-1 text-sm text-zinc-600 leading-snug">Let others in your group remind you to record your score. You'll never get more than one reminder per day.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Action bar --}}
                    <div class="mt-10 pt-6 border-t border-zinc-100 flex justify-end">
                        <x-form.input.button
                            type="submit"
                            loading-action="update"
                            class="w-36"
                            :primary="true"
                        >
                            Save Changes
                        </x-form.input.button>
                    </div>

                </form>

            </div>
        </div>

    </x-account.home-layout>

</x-layout.page-container>
