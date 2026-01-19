<div>

    <!-- Tabs -->
    <div
        x-data="{
        selectedId: null,
        init() {
            // Set the first available tab on the page on page load.
            this.$nextTick(() => this.select(this.$id('tab', 2)))
        },
        select(id) {
            this.selectedId = id
        },
        isSelected(id) {
            return this.selectedId === id
        },
        whichChild(el, parent) {
            return Array.from(parent.children).indexOf(el) + 1
        }
    }"
        x-id="['tab']"
        class="max-w-3xl mx-auto"

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
            class="flex flex-wrap items-center justify-center gap-2 rounded-full border border-gray-100 bg-white p-1 shadow-sm"
            x-cloak
        >
            <!-- Tab -->
            <li>
                <button
                    :id="$id('tab', whichChild($el.parentElement, $refs.tablist))"
                    @click="select($el.id)"
                    @mousedown.prevent
                    @focus="select($el.id)"
                    type="button"
                    :tabindex="isSelected($el.id) ? 0 : -1"
                    :aria-selected="isSelected($el.id)"
                    :class="isSelected($el.id) ? 'bg-green-700 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-full text-sm font-medium transition"
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
                    :class="isSelected($el.id) ? 'bg-green-700 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-full text-sm font-medium transition"
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
                    :class="isSelected($el.id) ? 'bg-green-700 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-full text-sm font-medium transition"
                    role="tab"
                >This Week
                </button>
            </li>
        </ul>

        <!-- Panels -->
        <div role="tabpanels" class="mt-6 rounded-2xl border border-gray-100 bg-white p-1 shadow-sm">
            <!-- Panel -->
            <section
                x-show="isSelected($id('tab', whichChild($el, $el.parentElement)))"
                :aria-labelledby="$id('tab', whichChild($el, $el.parentElement))"
                role="tabpanel"
                class="p-6"
                x-cloak
            >
                <x-layout.heading-divider class="mb-6 mt-2">Leaderboard</x-layout.heading-divider>

                @if($leaderboards->firstWhere('for', 'forever'))
                    <x-group.leaderboard
                        :group="$group"
                        :anonymize-private-users="$group->public && !$memberOfGroup"
                        :leaderboard="$leaderboards->firstWhere('for', 'forever')"
                    />

                    <div class="pt-10">
                        <x-layout.sub-heading class="text-center">Group Stats</x-layout.sub-heading>
                        <div class="mt-8">
                            <x-group.stats
                                :group="$group"
                                :leaderboard="$leaderboards->firstWhere('for', 'forever')"
                            />
                        </div>
                    </div>
                @else
                    <span class="text-sm md:text-base">
                        No one in this group has recorded any scores.
                    </span>
                @endif
            </section>

            <section
                x-show="isSelected($id('tab', whichChild($el, $el.parentElement)))"
                :aria-labelledby="$id('tab', whichChild($el, $el.parentElement))"
                role="tabpanel"
                class="p-6"
            >
                <x-layout.heading-divider class="mb-6 mt-2">Leaderboard</x-layout.heading-divider>

                @if($leaderboards->firstWhere('for', 'month'))
                    <x-group.leaderboard
                        :group="$group"
                        :anonymize-private-users="$group->public && !$memberOfGroup"
                        :leaderboard="$leaderboards->firstWhere('for', 'month')"
                    />

                    <div class="pt-10">
                        <x-layout.sub-heading class="text-center">Group Stats</x-layout.sub-heading>
                        <div class="mt-8">
                            <x-group.stats
                                :group="$group"
                                :leaderboard="$leaderboards->firstWhere('for', 'month')"
                            />
                        </div>
                    </div>


                @else
                    No one in this group has recorded any scores this month.
                @endif


            </section>

            <section
                x-show="isSelected($id('tab', whichChild($el, $el.parentElement)))"
                :aria-labelledby="$id('tab', whichChild($el, $el.parentElement))"
                role="tabpanel"
                class="p-6"
                x-cloak
            >

                <x-layout.heading-divider class="mb-6 mt-2">Leaderboard</x-layout.heading-divider>

                @if($leaderboards->firstWhere('for', 'week'))
                    <x-group.leaderboard
                        :group="$group"
                        :anonymize-private-users="$group->public && !$memberOfGroup"
                        :leaderboard="$leaderboards->firstWhere('for', 'week')"
                    />

                    <div class="pt-10">
                        <x-layout.sub-heading class="text-center">Group Stats</x-layout.sub-heading>
                        <div class="mt-8">
                            <x-group.stats
                                :group="$group"
                                :leaderboard="$leaderboards->firstWhere('for', 'week')"
                            />
                        </div>
                    </div>

                @else
                    No one in this group has recorded any scores this week.
                @endif
            </section>
        </div>
    </div>
</div>
