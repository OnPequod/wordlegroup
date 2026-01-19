<x-layout.page-container heading="Register A Wordle Group Account" title="Register A Wordle Group Account">
    <form wire:submit.prevent="store" class="mb-0 flex justify-center">
        <div class="w-full max-w-lg rounded-2xl border border-gray-100 bg-white p-6 sm:p-8 shadow-[0_10px_30px_var(--color-shadow)]">
            <div class="grid grid-cols-1 gap-y-6">
                @unless(Auth::check())
                    <x-form.input.text
                        wire:model="name" name="name" :errors="$errors" label="Your Name" placeholder="Your Name"
                    />
                    <x-form.input.text
                        wire:model="email"
                        name="email"
                        :errors="$errors"
                        type="email"
                        label="Email Address"
                        placeholder="my@email.com"
                    />
                @endunless
                <div>
                    <x-form.input.button loading-action="store">Register</x-form.input.button>
                </div>
            </div>
        </div>
    </form>
</x-layout.page-container>
