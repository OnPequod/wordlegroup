<x-layout.page-container :title="$user->name . '\'s Wordle Group Profile'" :heading="$user->name">
    <x-layout.social-meta
        title="Wordle Stats For {{ $user->name }}"
        :url="route('account.profile', $user)"
        description="View {{ $user->name }}'s Wordle statistics and score history on Wordle Group."
    />
    <x-layout.json-ld :schema="[
        '@context' => 'https://schema.org',
        '@type' => 'ProfilePage',
        'name' => $user->name . '\'s Wordle Profile',
        'description' => 'View ' . $user->name . '\'s Wordle statistics and score history.',
        'url' => route('account.profile', $user),
        'mainEntity' => [
            '@type' => 'Person',
            'name' => $user->name,
        ],
        'isPartOf' => ['@id' => url('/') . '#website'],
    ]" />

    <div class="grid grid-cols-1 gap-y-8 divide-y divide-gray-200">

        <div>
            <x-layout.sub-heading class="text-center">Stats</x-layout.sub-heading>
            <div class="mt-8">
                <x-account.stats :user="$user"/>
            </div>
        </div>

        <div class="pt-8">

            <x-layout.sub-heading class="text-center">Scores</x-layout.sub-heading>
            <div class="mt-8">
                <livewire:account.score-feed :user="$user"/>
            </div>
        </div>

        <div class="pt-8">

            <x-layout.sub-heading class="text-center">Groups</x-layout.sub-heading>
            <div class="mt-8">
                <x-account.groups-list :user="$user" :anonymize-private-groups="true"/>
            </div>
        </div>
    </div>

</x-layout.page-container>
