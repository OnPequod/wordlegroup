<x-layout.page-container heading="Create A New Wordle Group" title="Group a Wordle Group">
    <x-layout.social-meta
        title="Create A Wordle Group"
        :url="route('group.create')"
        description="Create a Wordle Group to keep score with your friends in Wordle."
    />
    <div class="flex justify-center">
        <div class="w-full max-w-lg rounded-2xl border border-gray-100 bg-white p-6 sm:p-8 shadow-[0_10px_30px_var(--color-shadow)]">
            <livewire:group.create-form/>
        </div>
    </div>
</x-layout.page-container>

