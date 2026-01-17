<div class="w-full">
    <form method="post" wire:submit.prevent="invite" class="mb-0">
        <div class="grid grid-cols-1 gap-y-6">
            <div class="col-span-1">
                <x-form.input.text
                    :errors="$errors"
                    name="name"
                    label="Invitee's Name"
                    placeholder="Jane Doe"
                    wire:model.blur="name"
                />
            </div>

            <div class="col-span-1">
                <x-form.input.text
                    :errors="$errors"
                    name="email"
                    type="email"
                    label="Email Address"
                    placeholder="email@address.com"
                    wire:model.blur="email"
                />
            </div>

            <div class="col-span-1 flex justify-center">
                <x-form.input.button class="w-36" loading-action="invite" :primary="false">
                    Send Invitation
                </x-form.input.button>
            </div>
        </div>
    </form>
</div>
