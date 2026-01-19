<x-layout.page-container title="Wordle Group - Keep Score With Friends" :top-padding="false">
    <x-layout.social-meta
        title="Wordle Group - Keep Score With Friends"
        :url="route('home')"
        description="A free and easy way to keep score with friends when playing Wordle. Create a group, invite friends, and see who climbs the leaderboard each day."
    />
    <div class="w-full mt-4 space-y-10">
        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
            <span
                class="inline-flex items-center rounded-full bg-wordle-yellow/20 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-green-900"
            >Already signed up?</span>
            <a class="link" href="{{ route('login') }}">Click here to log in.</a>
        </div>
        <div class="sm:max-w-2xl">
            <h1 class="text-4xl sm:text-5xl font-bold tracking-tight text-gray-900">Play Wordle With Friends</h1>
            <p class="mt-5 text-lg text-gray-600">Every day you text your group chat your score.</p>
            <p class="mt-4 text-lg text-gray-600">Now it's free and easy to keep score. All you need to do is click Share on your Wordle Board, select your email client, and email your board to <a class="link" href="mailto:scores@wordlegroup.com">scores@wordlegroup.com</a>. We'll do the rest.</p>
            <p class="mt-4 text-lg text-gray-600">To get started, just enter a name for your new group and your email.</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-6 sm:p-8 shadow-[0_10px_30px_var(--color-shadow)]">
            <x-layout.sub-heading text-color="text-green-700" class="mb-6">Create A Group</x-layout.sub-heading>
            <livewire:group.create-form :autofocus="false" />
        </div>
    </div>
</x-layout.page-container>
