<div class="bg-white rounded-xl border border-zinc-200/70 shadow-sm shadow-zinc-900/5 overflow-hidden">
    <div
        x-data="{
        selectedId: null,
        groupId: {{ $group->id }},
        init() {
            this.$nextTick(() => this.select(this.$id('tab', {{ $initialTab }}), true))
        },
        select(id, initial = false) {
            this.selectedId = id
            if (!initial) {
                this.saveTab(id)
            }
        },
        saveTab(id) {
            const tabIndex = parseInt(id.split('-').pop())
            const tabName = {1: 'forever', 2: 'month', 3: 'week'}[tabIndex] || 'month'
            fetch('/api/group/' + this.groupId + '/save-leaderboard-tab', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ tab: tabName })
            })
        },
        isSelected(id) {
            return this.selectedId === id
        },
        whichChild(el, parent) {
            return Array.from(parent.children).indexOf(el) + 1
        }
    }"
        x-id="['tab']"
    >
        <!-- Tab List -->
        <ul
            x-ref="tablist"
            @keydown.right.prevent.stop="$focus.wrap().next()"
            @keydown.home.prevent.stop="$focus.first()"
            @keydown.page-up.prevent.stop="$focus.first()"
            @keydown.left.prevent.stop="$focus.wrap().prev()"
            @keydown.end.prevent.stop="$focus.last()"
            @keydown.page-down.prevent.stop="$focus.last()"
            role="tablist"
            class="flex items-center justify-center gap-6 border-b border-zinc-100 px-6"
            x-cloak
        >
            <li>
                <button
                    :id="$id('tab', whichChild($el.parentElement, $refs.tablist))"
                    @click="select($el.id)"
                    @mousedown.prevent
                    @focus="select($el.id)"
                    type="button"
                    :tabindex="isSelected($el.id) ? 0 : -1"
                    :aria-selected="isSelected($el.id)"
                    :class="isSelected($el.id) ? 'text-green-700 border-green-700' : 'text-zinc-500 border-transparent hover:text-zinc-700 hover:border-zinc-300'"
                    class="inline-flex items-center justify-center px-1 py-4 border-b-2 -mb-[1px] text-sm font-semibold transition"
                    role="tab"
                >All Time
                </button>
            </li>

            <li>
                <button
                    :id="$id('tab', whichChild($el.parentElement, $refs.tablist))"
                    @click="select($el.id)"
                    @mousedown.prevent
                    @focus="select($el.id)"
                    type="button"
                    :tabindex="isSelected($el.id) ? 0 : -1"
                    :aria-selected="isSelected($el.id)"
                    :class="isSelected($el.id) ? 'text-green-700 border-green-700' : 'text-zinc-500 border-transparent hover:text-zinc-700 hover:border-zinc-300'"
                    class="inline-flex items-center justify-center px-1 py-4 border-b-2 -mb-[1px] text-sm font-semibold transition"
                    role="tab"
                >This Month
                </button>
            </li>

            <li>
                <button
                    :id="$id('tab', whichChild($el.parentElement, $refs.tablist))"
                    @click="select($el.id)"
                    @mousedown.prevent
                    @focus="select($el.id)"
                    type="button"
                    :tabindex="isSelected($el.id) ? 0 : -1"
                    :aria-selected="isSelected($el.id)"
                    :class="isSelected($el.id) ? 'text-green-700 border-green-700' : 'text-zinc-500 border-transparent hover:text-zinc-700 hover:border-zinc-300'"
                    class="inline-flex items-center justify-center px-1 py-4 border-b-2 -mb-[1px] text-sm font-semibold transition"
                    role="tab"
                >This Week
                </button>
            </li>
        </ul>

        <!-- Panels -->
        <div role="tabpanels" class="p-6">
            <!-- Panel: All Time -->
            <section
                x-show="isSelected($id('tab', whichChild($el, $el.parentElement)))"
                :aria-labelledby="$id('tab', whichChild($el, $el.parentElement))"
                role="tabpanel"
                x-cloak
            >
                @if($leaderboards->firstWhere('for', 'forever'))
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-zinc-700">Leaderboard</h3>
                        <p class="text-sm text-zinc-500">All time rankings</p>
                    </div>
                    <x-group.leaderboard
                        :group="$group"
                        :anonymize-private-users="$group->public && !$memberOfGroup"
                        :leaderboard="$leaderboards->firstWhere('for', 'forever')"
                    />

                    <div class="mt-6">
                        <x-group.stats
                            :group="$group"
                            :leaderboard="$leaderboards->firstWhere('for', 'forever')"
                        />
                    </div>
                @else
                    <p class="text-sm text-zinc-500">No one in this group has recorded any scores.</p>
                @endif
            </section>

            <!-- Panel: This Month -->
            <section
                x-show="isSelected($id('tab', whichChild($el, $el.parentElement)))"
                :aria-labelledby="$id('tab', whichChild($el, $el.parentElement))"
                role="tabpanel"
            >
                @if($leaderboards->firstWhere('for', 'month'))
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-zinc-700">Leaderboard</h3>
                        <p class="text-sm text-zinc-500">This month's rankings</p>
                    </div>
                    <x-group.leaderboard
                        :group="$group"
                        :anonymize-private-users="$group->public && !$memberOfGroup"
                        :leaderboard="$leaderboards->firstWhere('for', 'month')"
                    />

                    <div class="mt-6">
                        <x-group.stats
                            :group="$group"
                            :leaderboard="$leaderboards->firstWhere('for', 'month')"
                        />
                    </div>
                @else
                    <p class="text-sm text-zinc-500">No one in this group has recorded any scores this month.</p>
                @endif
            </section>

            <!-- Panel: This Week -->
            <section
                x-show="isSelected($id('tab', whichChild($el, $el.parentElement)))"
                :aria-labelledby="$id('tab', whichChild($el, $el.parentElement))"
                role="tabpanel"
                x-cloak
            >
                @if($leaderboards->firstWhere('for', 'week'))
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-zinc-700">Leaderboard</h3>
                        <p class="text-sm text-zinc-500">This week's rankings</p>
                    </div>
                    <x-group.leaderboard
                        :group="$group"
                        :anonymize-private-users="$group->public && !$memberOfGroup"
                        :leaderboard="$leaderboards->firstWhere('for', 'week')"
                    />

                    <div class="mt-6">
                        <x-group.stats
                            :group="$group"
                            :leaderboard="$leaderboards->firstWhere('for', 'week')"
                        />
                    </div>
                @else
                    <p class="text-sm text-zinc-500">No one in this group has recorded any scores this week.</p>
                @endif
            </section>
        </div>
    </div>
</div>
