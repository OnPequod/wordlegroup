<x-layout.page-container title="My Groups" :wide="true" :top-padding="false">

    <x-account.home-layout page="groups">

        <div class="flex flex-col gap-8 pb-12">
            {{-- Header --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 font-serif">My Groups</h1>
                    <p class="mt-1 text-sm text-zinc-500">Wordle groups you belong to</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a
                        class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800"
                        href="{{ route('group.create') }}"
                    >Create a group</a>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-zinc-200/70 shadow-sm shadow-zinc-900/5 p-8">
                <x-account.groups-list :user="$user" />
            </div>
        </div>

    </x-account.home-layout>

</x-layout.page-container>
