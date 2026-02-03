<div>
    {{-- Score cards --}}
    @if($scores->isNotEmpty())
        <ul role="list" class="grid grid-cols-1 lg:grid-cols-2">
            @foreach($scores as $score)
                <x-score.card
                    :score="$score"
                    :show-user="true"
                    :anonymize-private-users="$anonymizePrivateUsers"
                    :flat="true"
                />
            @endforeach
        </ul>
    @else
        <div class="p-6 text-center text-sm text-zinc-500">
            No scores recorded yet.
        </div>
    @endif

    {{-- Footer with filter and pagination --}}
    <div class="px-6 py-4 border-t border-zinc-100 bg-zinc-50/30">
        <div class="flex items-center justify-between gap-3">
            {{-- Left: Filter + Per page --}}
            <div class="flex items-center gap-2">
                @if($this->isGroupMember)
                    <div x-data="{ open: false }" class="relative">
                        <button
                            type="button"
                            @click="open = !open"
                            class="p-1.5 rounded-md transition {{ $filterByUserId ? 'bg-green-50 text-green-700' : 'text-zinc-400 hover:text-zinc-600' }}"
                            title="Filter by member"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                            </svg>
                        </button>
                        <div
                            x-show="open"
                            @click.outside="open = false"
                            x-transition
                            class="absolute left-0 bottom-full mb-2 w-48 bg-white rounded-lg border border-zinc-200 shadow-lg z-10 p-4"
                        >
                            <label class="block text-xs font-medium text-zinc-500 mb-3">Filter by user</label>
                            <x-group.user-select
                                :default-empty="true"
                                wire:model.live="filterByUserId"
                                name="activityFeedUsers"
                                :group="$this->group"
                            />
                            @if($filterByUserId)
                                <button
                                    class="mt-2 text-xs text-zinc-500 hover:text-zinc-900 transition"
                                    type="button"
                                    wire:click="$set('filterByUserId', null)"
                                    @click="open = false"
                                >
                                    Clear filter
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
                <select
                    wire:model.live="perPage"
                    wire:change="$refresh"
                    class="text-xs border-zinc-200 rounded-md py-1 pl-2 pr-7 text-zinc-600 focus:ring-green-600 focus:border-green-600"
                >
                    <option value="6">6</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                </select>
            </div>

            {{-- Right: Pagination --}}
            @if($scores->hasPages())
                <div class="flex items-center gap-2">
                    {{ $scores->onEachSide(1)->links('vendor.pagination.simple-tailwind') }}
                </div>
            @endif
        </div>
    </div>
</div>
