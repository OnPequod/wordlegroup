<x-layout.page-container
    :error-message="$errors->has('attempt') ? $errors->get('attempt') : null"
    heading="Log In To Wordle Group"
    title="Log In To Wordle Group"
>
    <x-layout.social-meta/>
    <form
        @if($codeSent)
        wire:submit.prevent="attemptLoginWithCode"
        @else
        wire:submit.prevent="send"
        @endif
        class="grid grid-cols-1 gap-y-6 w-full max-w-lg mx-auto rounded-2xl border border-gray-100 bg-white p-6 sm:p-8 shadow-[0_10px_30px_var(--color-shadow)]"
        x-data
        x-init="$nextTick(() => { if (document.getElementById('loginCode')) document.getElementById('loginCode').focus() })"
    >

        @if($codeSent)

            <div
                wire:key="emailSent"
                class="bg-green-50 border border-green-700 mb-2 rounded text-sm px-3 py-2 text-green-800"
            >
                @if($codeResent)
                    We have sent you another login code. Enter it below.
                @else
                    We have emailed you a login code. Enter it below.
                @endif
            </div>

            <x-form.input.text
                :errors="$errors"
                class="font-fixed"
                name="loginCode"
                label="Login Code"
                wire:model.blur="loginCode"
                placeholder="123456"
            />

            <div class="col-span-1">
                <x-form.input.button type="submit" loading-action="attemptLoginWithCode" class="w-44">Log In
                </x-form.input.button>
            </div>

            <div class="col-span-1">
                <button class="text-xs text-gray-500 hover:text-gray-600" type="button" wire:click="sendAgain">
                    I didn't receive an email. Send me another one.
                </button>
            </div>
        @else

            <x-form.input.text
                :errors="$errors"
                name="email"
                autofocus
                label="Email Address"
                wire:model.blur="email"
                placeholder="your@email.address"
            />

            <div class="col-span-1">
                <x-form.input.button type="submit" loading-action="send" class="w-44">Send Log In Code
                </x-form.input.button>
            </div>
        @endif

    </form>

    <div class="text-center mt-10 text-sm">
        <a class="font-semibold text-zinc-600 hover:text-green-700 transition" href="{{ route('register') }}">Don't have an account? Register one here.</a>
    </div>
</x-layout.page-container>
