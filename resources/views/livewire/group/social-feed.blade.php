<div class="flex flex-col gap-8">
    {{-- Group Activity Section --}}
    <div class="rounded-2xl bg-white border border-zinc-200 shadow-sm overflow-hidden">
        {{-- Activity Content --}}
        @if($this->hasScores)
            <livewire:group.activity-feed
                :group="$group"
                :anonymize-private-users="$anonymizePrivateUsers"
                :filter-by-user-id="$filterByUserId"
                :key="'activity-feed-' . $group->id . '-' . ($filterByUserId ?? 'all')"
            />
        @else
            <div class="px-6 py-8 text-center">
                <p class="text-sm text-zinc-500">No scores recorded yet. Be the first to record a score!</p>
            </div>
        @endif
    </div>

    {{-- Discussion Section --}}
    <div class="rounded-2xl bg-white border border-zinc-200 shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-5 border-b border-zinc-100">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-semibold text-zinc-900">Discussion</h3>
                    @if($this->unreadDiscussionCount > 0)
                        <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-xs font-semibold text-white bg-green-600 rounded-full">
                            {{ $this->unreadDiscussionCount > 99 ? '99+' : $this->unreadDiscussionCount }}
                        </span>
                    @endif
                </div>
                <p class="text-sm text-zinc-500 mt-0.5">Chat with your group members</p>
            </div>
        </div>

        {{-- Discussion Content --}}
        <livewire:group.discussion-feed
            :group="$group"
            :view-mode="$viewMode"
            :filter-by-user-id="$filterByUserId"
            :key="'discussion-feed-' . $group->id . '-' . $viewMode . '-' . ($filterByUserId ?? 'all')"
        />
    </div>
</div>
