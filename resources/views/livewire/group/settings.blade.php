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

            <div class="max-w-md mx-auto">
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
            </div>
        </div>

    </x-account.home-layout>

</x-layout.page-container>
