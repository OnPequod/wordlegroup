<x-layout.page-container title="Record My Score" :top-padding="false">

    <x-account.home-layout page="record-score">

        <div class="flex flex-col gap-8 pb-12">
            {{-- Header --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 font-serif">Record Score</h1>
                    <p class="mt-1 text-sm text-zinc-500">Save your Wordle result</p>
                </div>
            </div>

            <livewire:score.record-form :user="$user" />
        </div>

    </x-account.home-layout>

</x-layout.page-container>
