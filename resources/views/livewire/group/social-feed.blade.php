<div class="rounded-2xl bg-white border border-zinc-200 shadow-sm overflow-hidden">
    {{-- Header: Tabs left, Controls right --}}
    <div class="flex items-center justify-between border-b border-zinc-200 px-4">
        {{-- Tabs --}}
        <div class="flex items-center gap-6">
            <button
                type="button"
                wire:click="switchToActivity"
                class="px-1 py-4 text-sm font-semibold transition border-b-2 -mb-[1px] {{ $activeTab === 'activity' ? 'text-green-700 border-green-700' : 'text-zinc-500 border-transparent hover:text-zinc-700 hover:border-zinc-300' }}"
            >
                Activity
                @if($this->newActivityCount > 0 && $activeTab !== 'activity')
                    <span class="ml-1.5 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-semibold text-white bg-green-600 rounded-full">
                        {{ $this->newActivityCount > 99 ? '99+' : $this->newActivityCount }}
                    </span>
                @endif
            </button>
            <button
                type="button"
                wire:click="switchToDiscussion"
                class="px-1 py-4 text-sm font-semibold transition border-b-2 -mb-[1px] relative {{ $activeTab === 'discussion' ? 'text-green-700 border-green-700' : 'text-zinc-500 border-transparent hover:text-zinc-700 hover:border-zinc-300' }}"
            >
                Discussion
                @if($this->unreadDiscussionCount > 0 && $activeTab !== 'discussion')
                    <span class="ml-1.5 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-semibold text-white bg-green-600 rounded-full">
                        {{ $this->unreadDiscussionCount > 99 ? '99+' : $this->unreadDiscussionCount }}
                    </span>
                @endif
            </button>
        </div>

        {{-- Controls --}}
        <div class="flex items-center gap-2">
            {{-- View mode toggle --}}
            <div class="flex items-center rounded-lg border border-zinc-200 p-0.5">
                <button
                    type="button"
                    wire:click="$set('viewMode', 'tiles')"
                    class="p-1.5 rounded-md transition {{ $viewMode === 'tiles' ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-400 hover:text-zinc-600' }}"
                    title="Tile view"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                </button>
                <button
                    type="button"
                    wire:click="$set('viewMode', 'list')"
                    class="p-1.5 rounded-md transition {{ $viewMode === 'list' ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-400 hover:text-zinc-600' }}"
                    title="List view"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </button>
            </div>

            {{-- Filter popover --}}
            @if($this->isGroupMember)
                <div x-data="{ open: false }" class="relative">
                    <button
                        type="button"
                        @click="open = !open"
                        class="p-1.5 rounded-md border transition {{ $filterByUserId ? 'border-green-600 bg-green-50 text-green-700' : 'border-zinc-200 text-zinc-400 hover:text-zinc-600 hover:border-zinc-300' }}"
                        title="Filter by user"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                        </svg>
                    </button>
                    <div
                        x-show="open"
                        @click.outside="open = false"
                        x-transition
                        class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg border border-zinc-200 shadow-lg z-10 p-3"
                    >
                        <label class="block text-xs font-medium text-zinc-500 mb-2">Filter by user</label>
                        <x-group.user-select
                            :default-empty="true"
                            wire:model.live="filterByUserId"
                            name="socialFeedUsers"
                            :group="$group"
                        />
                        @if($filterByUserId)
                            <button
                                class="mt-2 text-xs text-zinc-500 hover:text-zinc-900 transition"
                                type="button"
                                wire:click="clearUserFilter"
                                @click="open = false"
                            >
                                Clear filter
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Loading State --}}
    <div wire:loading.flex class="items-center justify-center py-12">
        <svg class="animate-spin h-5 w-5 text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

    {{-- Tab Content --}}
    <div wire:loading.remove>
        @if($activeTab === 'activity')
            @if($this->hasScores)
                <livewire:group.activity-feed
                    :group="$group"
                    :anonymize-private-users="$anonymizePrivateUsers"
                    :view-mode="$viewMode"
                    :filter-by-user-id="$filterByUserId"
                    :key="'activity-feed-' . $group->id . '-' . $viewMode . '-' . ($filterByUserId ?? 'all')"
                />
            @else
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-zinc-500">No activity yet. Be the first to record a score!</p>
                </div>
            @endif
        @else
            <livewire:group.discussion-feed
                :group="$group"
                :view-mode="$viewMode"
                :filter-by-user-id="$filterByUserId"
                :key="'discussion-feed-' . $group->id . '-' . $viewMode . '-' . ($filterByUserId ?? 'all')"
            />
        @endif
    </div>
</div>
