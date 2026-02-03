<x-layout.page-container title="Wordle Group Settings" :wide="true" :top-padding="false">

    <x-account.home-layout :page="'group.' . $group->id . '.settings'">

        <div class="flex flex-col gap-8 pb-12">
            {{-- Header --}}
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-zinc-900 font-serif">{{ $group->name }} Settings</h1>
                    <p class="mt-1 text-sm text-zinc-500">Manage your group preferences</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a
                        class="rounded-md border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50"
                        href="{{ route('group.home', $group) }}"
                    >Back to group</a>
                </div>
            </div>

            <div class="max-w-2xl mx-auto space-y-6">
                <div class="bg-white rounded-xl border border-zinc-200/70 shadow-sm shadow-zinc-900/5 p-8">
                    <form type="PATCH" class="mb-0" wire:submit.prevent="update">
                        <div class="space-y-6">
                            <x-form.input.text
                                :errors="$errors"
                                name="name"
                                label="Name"
                                wire:model="name"
                                placeholder="Name"
                            />

                            <x-group.user-select
                                name="adminUserId"
                                label="Group Administrator"
                                :errors="$errors"
                                :group="$group"
                                :selected-user-id="$adminUserId"
                                wire:model="adminUserId"
                            />

                            <x-form.input.checkbox
                                name="public"
                                wire:model="public"
                                label="Make this group public."
                                tip="This will allow non-group members to see the group page. Users whose profiles are set to private will have their names anonymized."
                            />

                            @if($initialAdminUserId !== $adminUserId)
                                <x-form.input.checkbox
                                    :errors="$errors"
                                    name="confirmTransfer"
                                    wire:model="confirmTransfer"
                                    label="Confirm Transfer"
                                    tip="After transferring administrators, you will no longer be able to administrate this group."
                                />
                            @endif

                            <div class="pt-4">
                                <x-form.input.button
                                    type="submit"
                                    loading-action="update"
                                    :primary="true"
                                >Save Changes</x-form.input.button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Member List --}}
                <livewire:group.member-list :group="$group"/>

                {{-- Export Group Scores --}}
                <div class="bg-white rounded-xl border border-zinc-200/70 shadow-sm shadow-zinc-900/5 p-8">
                    <h3 class="text-lg font-semibold text-zinc-900 pb-3 border-b border-zinc-100">Export Group Scores</h3>
                    <div class="pt-5">
                        <p class="text-sm text-zinc-600 mb-2">Download all scores from this group as a CSV file.</p>
                        <p class="text-xs text-zinc-500 mb-4">Each member is assigned a sequential ID based on when they joined the group.</p>
                        <a
                            href="{{ route('group.export.scores.csv', $group) }}"
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

    </x-account.home-layout>

</x-layout.page-container>
