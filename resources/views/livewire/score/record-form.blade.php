<div x-data="{ activeMethod: 'paste' }">
    @once
        @push('meta')
{{--            <meta name="turbo-cache-control" content="no-cache">--}}
        @endpush
    @endonce

    {{-- Email prompt (only on full page, not quick mode) --}}
    @unless($quick)
        @unless($user->dismissed_email_notification || $hideEmail)
            <div class="rounded-xl border border-zinc-200 bg-zinc-50/50 p-5 mb-6">
                <h3 class="text-sm font-semibold text-zinc-900">Email Your Scores</h3>
                <x-score.email-prompt class="mt-2 text-sm text-zinc-600"/>
                <div class="mt-3">
                    <livewire:score.dismiss-email-prompt-notification
                        :user="$user"
                        class="text-xs text-zinc-500 hover:text-zinc-700"
                        back-route="account.record-score"
                    />
                </div>
            </div>
        @endunless
    @endunless

    {{-- Quick mode: Toggle between methods --}}
    @if($quick)
        <div class="space-y-5">
            {{-- Paste board section (quick mode) --}}
            <div x-show="activeMethod === 'paste'">
                <form wire:submit.prevent="recordScoreFromBoard" class="mb-0">
                    @if($group && $isGroupAdmin)
                        <div class="mb-4">
                            <x-group.user-select
                                name="user"
                                wire:model="recordForUserId"
                                :group="$group"
                                :selected-user-id="$user->id"
                            />
                        </div>
                    @endif
                    <x-form.input.textarea
                        :errors="$errors"
                        name="board"
                        label="Board"
                        :rows="6"
                        placeholder="Wordle 250 3/6..."
                        wire:model.blur="board"
                        class="font-mono text-xs"
                    />
                    <div class="mt-4 flex items-center justify-between">
                        <x-form.input.button loading-action="recordScoreFromBoard" class="w-36" :primary="false">
                            Record Score
                        </x-form.input.button>
                        <button
                            type="button"
                            class="text-sm font-medium text-green-700 hover:text-green-800"
                            @click="activeMethod = 'manual'"
                        >
                            I don't have the board
                        </button>
                    </div>
                </form>
            </div>

            {{-- Manual entry section (quick mode) --}}
            <div x-show="activeMethod === 'manual'" x-cloak>
                <form wire:submit.prevent="recordScoreManually" class="mb-0">
                    <div class="space-y-4">
                        @if($group && $isGroupAdmin)
                            <x-group.user-select
                                name="user"
                                wire:model="recordForUserId"
                                :group="$group"
                                :selected-user-id="$user->id"
                            />
                        @endif
                        <div class="grid grid-cols-2 gap-4">
                            <x-form.input.date
                                :errors="$errors"
                                name="date"
                                label="Date"
                                :placeholder="$date"
                                :options="['defaultDate' => $date]"
                                wire:model="date"
                            />
                            <x-form.input.text
                                :errors="$errors"
                                name="boardNumber"
                                type="number"
                                label="Board #"
                                placeholder="123"
                                min="1"
                                max="10000"
                                wire:model.blur="boardNumber"
                            />
                        </div>
                        <x-form.input.text
                            :errors="$errors"
                            name="score"
                            type="number"
                            label="Score"
                            placeholder="3"
                            min="1"
                            max="6"
                            wire:model.blur="score"
                        />
                        <div class="flex items-center gap-6">
                            <x-form.input.checkbox
                                name="bricked"
                                label="X/6 (Missed)"
                                wire:model="bricked"
                            />
                            <x-form.input.checkbox
                                name="hardMode"
                                label="Hard Mode"
                                wire:model="hardMode"
                            />
                        </div>

                        {{-- Bot Scores (Quick Mode) --}}
                        <div class="pt-3 border-t border-zinc-100">
                            <p class="text-xs text-zinc-500 mb-2">WordleBot scores (optional)</p>
                            <div class="grid grid-cols-2 gap-3">
                                <x-form.input.text
                                    :errors="$errors"
                                    name="botSkillScore"
                                    type="number"
                                    label="Skill"
                                    placeholder="0-99"
                                    min="0"
                                    max="99"
                                    wire:model.blur="botSkillScore"
                                />
                                <x-form.input.text
                                    :errors="$errors"
                                    name="botLuckScore"
                                    type="number"
                                    label="Luck"
                                    placeholder="0-99"
                                    min="0"
                                    max="99"
                                    wire:model.blur="botLuckScore"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <x-form.input.button loading-action="recordScoreManually" class="w-36" :primary="false">
                            Record Score
                        </x-form.input.button>
                        <button
                            type="button"
                            class="text-sm font-medium text-green-700 hover:text-green-800"
                            @click="activeMethod = 'paste'"
                        >
                            I have the board
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        {{-- Full page mode: Two distinct section cards --}}
        <div class="space-y-6">
            {{-- Section 1: Paste board --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-8">
                <div class="mb-6">
                    <h3 class="text-base font-semibold text-zinc-900">Paste Your Board</h3>
                    <p class="mt-1 text-sm text-zinc-600">Copy the share text from Wordle and paste it below. We'll parse the date, score, and board automatically.</p>
                </div>

                <form wire:submit.prevent="recordScoreFromBoard" class="mb-0">
                    <div class="space-y-4">
                        @if($group && $isGroupAdmin)
                            <x-group.user-select
                                name="user"
                                wire:model="recordForUserId"
                                :group="$group"
                                :selected-user-id="$user->id"
                                label="Recording for"
                            />
                        @endif

                        <x-form.input.textarea
                            :errors="$errors"
                            name="board"
                            label="Wordle Share Text"
                            :rows="7"
                            placeholder="Wordle 1,234 4/6

⬜⬜⬜⬜⬜
⬜⬜⬜⬜⬜
⬜⬜⬜⬜⬜
⬜⬜⬜⬜⬜"
                            wire:model.blur="board"
                            class="font-mono text-xs"
                        />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-form.input.button
                            loading-action="recordScoreFromBoard"
                            class="w-40"
                            :primary="true"
                        >
                            Record Score
                        </x-form.input.button>
                    </div>
                </form>
            </div>

            {{-- Section 2: Manual entry --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-8">
                <div class="mb-6">
                    <h3 class="text-base font-semibold text-zinc-900">Enter Manually</h3>
                    <p class="mt-1 text-sm text-zinc-600">Don't have your board? Enter the date and score manually.</p>
                </div>

                <form wire:submit.prevent="recordScoreManually" class="mb-0">
                    <div class="space-y-4">
                        @if($group && $isGroupAdmin)
                            <x-group.user-select
                                name="user"
                                wire:model="recordForUserId"
                                :group="$group"
                                :selected-user-id="$user->id"
                                label="Recording for"
                            />
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form.input.date
                                :errors="$errors"
                                name="date"
                                label="Date"
                                :placeholder="$date"
                                :options="['defaultDate' => $date]"
                                wire:model="date"
                            />
                            <x-form.input.text
                                :errors="$errors"
                                name="boardNumber"
                                type="number"
                                label="Wordle Board Number"
                                tip="Overrides date if provided"
                                placeholder="1234"
                                min="1"
                                max="10000"
                                wire:model.blur="boardNumber"
                            />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-form.input.text
                                :errors="$errors"
                                name="score"
                                type="number"
                                label="Score (1-6)"
                                placeholder="4"
                                min="1"
                                max="6"
                                wire:model.blur="score"
                            />
                            <div class="flex items-end pb-3">
                                <div class="flex items-center gap-6">
                                    <x-form.input.checkbox
                                        name="bricked"
                                        label="X/6 (Missed)"
                                        wire:model="bricked"
                                    />
                                    <x-form.input.checkbox
                                        name="hardMode"
                                        label="Hard Mode"
                                        wire:model="hardMode"
                                    />
                                </div>
                            </div>
                        </div>

                        {{-- Bot Scores Section --}}
                        <div class="pt-4 border-t border-zinc-100">
                            <div class="mb-3">
                                <h4 class="text-sm font-medium text-zinc-700">WordleBot Scores (Optional)</h4>
                                <p class="mt-1 text-xs text-zinc-500">If you use the NY Times WordleBot, you can record your skill and luck scores.</p>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <x-form.input.text
                                    :errors="$errors"
                                    name="botSkillScore"
                                    type="number"
                                    label="Skill Score"
                                    tip="How well you played given the information available (0-99)"
                                    placeholder="85"
                                    min="0"
                                    max="99"
                                    wire:model.blur="botSkillScore"
                                />
                                <x-form.input.text
                                    :errors="$errors"
                                    name="botLuckScore"
                                    type="number"
                                    label="Luck Score"
                                    tip="How favorable your guesses turned out to be (0-99)"
                                    placeholder="72"
                                    min="0"
                                    max="99"
                                    wire:model.blur="botLuckScore"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-form.input.button
                            loading-action="recordScoreManually"
                            class="w-40"
                            :primary="true"
                        >
                            Record Score
                        </x-form.input.button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
