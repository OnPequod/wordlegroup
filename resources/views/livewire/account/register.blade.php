<x-layout.page-container heading="Register A Wordle Group Account" title="Register A Wordle Group Account">
    <x-layout.social-meta
        title="Create Account - Wordle Group"
        :url="route('register')"
        description="Create a free Wordle Group account to track your scores, join groups, and compete on the leaderboard."
    />
    <form wire:submit.prevent="store" class="mb-0 flex justify-center">
        <div class="w-full max-w-lg rounded-2xl border border-gray-100 bg-white p-6 sm:p-8 shadow-[0_10px_30px_var(--color-shadow)]">
            <div class="grid grid-cols-1 gap-y-6">
                @unless(Auth::check())
                    <x-form.input.text
                        wire:model="name" name="name" :errors="$errors" label="Your Name" placeholder="Your Name"
                    />
                    <x-form.input.text
                        wire:model="email"
                        name="email"
                        :errors="$errors"
                        type="email"
                        label="Email Address"
                        placeholder="my@email.com"
                    />

                    {{-- Public Leaderboard Options --}}
                    <div class="border-t border-zinc-100 pt-6">
                        <h3 class="text-sm font-semibold text-zinc-900 mb-3">Public Leaderboard</h3>
                        <p class="text-sm text-zinc-600 mb-4">Choose whether to participate in the global Wordle leaderboard.</p>

                        <label
                            for="showOnPublicLeaderboard"
                            class="flex items-start gap-3 py-3 px-3 -mx-3 rounded-lg cursor-pointer hover:bg-zinc-50/50 transition"
                        >
                            <input
                                type="checkbox"
                                id="showOnPublicLeaderboard"
                                wire:model.live="showOnPublicLeaderboard"
                                class="mt-1 h-4 w-4 rounded border-zinc-300 text-green-700 focus:ring-green-600"
                            >
                            <div class="flex-1">
                                <span class="text-sm font-medium text-zinc-900">Participate in the public leaderboard</span>
                                <p class="mt-1 text-xs text-zinc-500">Your scores will be included. By default, you will appear as "Anonymous".</p>
                            </div>
                        </label>

                        <label
                            for="showNameOnPublicLeaderboard"
                            class="flex items-start gap-3 py-3 px-3 -mx-3 rounded-lg cursor-pointer hover:bg-zinc-50/50 transition {{ !$showOnPublicLeaderboard ? 'opacity-50' : '' }}"
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
                                <p class="mt-1 text-xs text-zinc-500">Show your name instead of "Anonymous".</p>
                            </div>
                        </label>
                    </div>
                @endunless
                <div>
                    <x-form.input.button loading-action="store">Register</x-form.input.button>
                </div>
            </div>
        </div>
    </form>
</x-layout.page-container>
