<x-layout.page-container heading="Create A New Wordle Group" title="Create a Wordle Group">
    <x-layout.social-meta
        title="Create A Wordle Group"
        :url="route('group.create')"
        description="Create a Wordle Group to keep score with your friends in Wordle."
    />
    <x-layout.json-ld :schema="[
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => 'Create A Wordle Group',
        'description' => 'Create a Wordle Group to keep score with your friends in Wordle.',
        'url' => route('group.create'),
        'isPartOf' => ['@id' => url('/') . '#website'],
    ]" />
    <div class="flex justify-center">
        <div class="w-full max-w-lg rounded-2xl border border-gray-100 bg-white p-6 sm:p-8 shadow-[0_10px_30px_var(--color-shadow)]">
            <livewire:group.create-form/>
        </div>
    </div>
</x-layout.page-container>

