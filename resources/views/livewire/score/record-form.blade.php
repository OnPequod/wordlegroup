<div
    class="grid grid-cols-1 @if($quick) gap-y-5 @else gap-y-8 @endif divide-y divide-gray-200"
    @if($quick)
    x-data="{show: 'haveBoard'}"
    @endif
>

    @push('meta')
{{--        <meta name="turbo-cache-control" content="no-cache">--}}
    @endpush

    @unless($user->dismissed_email_notification || $hideEmail)
        <div class="col-span-1">

            <h2 class="text-green-700 text-lg font-semibold">
                Email Your Scores
            </h2>
            <x-score.email-prompt class="mt-4 text-sm"/>
            <div class="mt-6">
                <livewire:score.dismiss-email-prompt-notification
                    :user="$user"
                    class="text-xs text-gray-600 hover:text-gray-800"
                    back-route="account.record-score"
                />
            </div>
        </div>
    @endunless

    <div
        class="col-span-1"
        @if($quick)
        x-show="show === 'haveBoard'"
        @endif
    >
        <form wire:submit.prevent="recordScoreFromBoard" class="mt-8 mb-0">
            @unless($quick)
                <h2 class="text-green-700 text-lg font-semibold mb-6">
                    @if($recordingForSelf)
                        I Have My Board
                    @else
                        I Have The Board
                    @endif
                </h2>
            @endunless

            <div class="grid grid-cols-1 @if($quick) gap-y-5 @else gap-y-8 @endif">
                @if($group && $isGroupAdmin)
                    <x-group.user-select
                        name="user"
                        wire:model="recordForUserId"
                        :group="$group"
                        :selected-user-id="$user->id"
                    />
                @endif
                <div class="col-span-1">
                    <x-form.input.textarea
                        :errors="$errors"
                        name="board"
                        label="Board"
                        :rows="7"
                        :tip="$quick ? '' : 'Just paste in your board and we\'ll figure out the dare and score and save your board.'"
                        placeholder="Wordle 250 3/6..."
                        wire:model.blur="board"
                        class="font-system"
                    />
                </div>

                <div class="col-span-1 flex items-center justify-between">
                    <x-form.input.button
                        loading-action="recordScoreFromBoard" class="w-44 font-semibold" :primary="! $quick"
                    >
                        Record Score
                    </x-form.input.button>
                    @if($quick)
                        <button
                            type="button"
                            class="text-sm text-green-700 hover:text-green-800"
                            @click="show = 'manual'"
                        >I don't have the board.
                        </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
    <div
        class="col-span-1"
        @if($quick)
        x-show="show === 'manual'"
        x-cloak
        @endif
    >
        <form wire:submit.prevent="recordScoreManually" class="mt-8 mb-0">

            @unless($quick)
                <h2 class="text-green-700 text-lg font-semibold">
                    @if($recordingForSelf)
                        I Don't Have My Board
                    @else
                        I Don't Have The Board
                    @endif
                </h2>
            @endunless

            <div class="grid grid-cols-a @if($quick) gap-y-5 @else gap-y-8 mt-8 @endif">
                @if($group && $isGroupAdmin)
                    <x-group.user-select
                        name="user"
                        wire:model="recordForUserId"
                        :group="$group"
                        :selected-user-id="$user->id"
                    />
                @endif
                <div class="col-span-1">
                    <x-form.input.date
                        :errors="$errors"
                        name="date"
                        label="Date"
                        :placeholder="$date"
                        :options="['defaultDate' => $date]"
                        wire:model="date"
                    />
{{--                    <div class="col-span-1 pt-2 pb-4">--}}
{{--                        <span class="text-gray-500 italic">or</span>--}}
{{--                    </div>--}}
                </div>
                    <div class="col-span-1">
                        <x-form.input.text
                            :errors="$errors"
                            name="boardNumber"
                            type="number"
                            label="Wordle Board Number"
                            tip="The date is ignored if a board number is entered."
                            placeholder="123"
                            min="1"
                            max="10000"
                            wire:model.blur="boardNumber"
                        />
                    </div>

                <div class="col-span-1">
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
                    <div class="@if($errors->has('score')) mt-4 @else mt-1 @endif text-gray-500 text-sm">
                        Click the checkbox below if you missed.
                    </div>

                    <div class="mt-4">
                        <x-form.input.checkbox
                            name="bricked"
                            label="X/6"
                            wire:model="bricked"
                            tip="Oops, I bricked out."
                        />
                    </div>

                    <div class="mt-4">
                        <x-form.input.checkbox
                            name="hardMode"
                            label="Hard Mode"
                            wire:model="hardMode"
                        />
                    </div>

                </div>

                <div class="col-span-1 flex items-center justify-between">
                    <x-form.input.button
                        loading-action="recordScoreManually" class="w-44  font-semibold" :primary="! $quick"
                    >
                        @if($recordingForSelf)
                            Record My Score
                        @else
                            Record Score
                        @endif
                    </x-form.input.button>
                    @if($quick)
                        <button
                            type="button"
                            class="text-sm text-green-700 hover:text-green-800"
                            @click="show = 'haveBoard'"
                        >I have the board.
                        </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
