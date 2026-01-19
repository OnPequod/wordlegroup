<div class="rounded-2xl bg-white border border-zinc-200 shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-5 border-b border-zinc-100">
        <h3 class="text-lg font-semibold text-zinc-900">Invite Someone</h3>
        <p class="text-sm text-zinc-500 mt-0.5">Send an email invitation to join {{ $group->name }}</p>
    </div>

    {{-- Form --}}
    <form method="post" wire:submit.prevent="invite" class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <x-form.input.text
                    :errors="$errors"
                    name="name"
                    label="Name"
                    placeholder="Jane Doe"
                    wire:model.blur="name"
                />
            </div>
            <div>
                <x-form.input.text
                    :errors="$errors"
                    name="email"
                    type="email"
                    label="Email"
                    placeholder="jane@example.com"
                    wire:model.blur="email"
                />
            </div>
        </div>
        <div class="mt-5 flex justify-end">
            <x-form.input.button
                class="w-full sm:w-auto"
                loading-action="invite"
                :primary="true"
            >
                Send Invitation
            </x-form.input.button>
        </div>
    </form>
</div>
