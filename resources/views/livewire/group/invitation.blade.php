<x-layout.page-container :heading="'Join ' . $invitation->group->name" :title="'Join ' . $invitation->group->name">
    <div class="flex justify-center">
        <div class="w-full max-w-sm">

            <div class="text-gray-600 pb-8 text-sm">
                <p>
                    {{ $invitation->invitingUser->name }} has invited you to join the Wordle Group <span class="font-semibold">{{ $invitation->group->name }}</span>.
                </p>
                <p class="mt-4">
                    Just click "Join Group" below. Good luck!
                </p>
            </div>

            <form class="mb-0" method="post" wire:submit.prevent="accept">
                <div class="grid grid-cols-1 gap-y-6">
                    <div class="col-span-1">
                        <x-form.input.text
                            :errors="$errors"
                            name="name"
                            autofocus
                            label="Your Name"
                            placeholder="Jane Doe"
                            wire:model.blur="name"
                        />
                    </div>


                    <x-form.input.checkbox
                        name="publicProfile"
                        wire:model="publicProfile"
                        label="Make my profile public."
                        tip="Select this if you want to be able to share your stats with anyone who is not a member of your Wordle Groups."
                    />

                    <x-form.input.checkbox
                        name="allowDigestEmails"
                        wire:model="allowDigestEmails"
                        label="Receive weekly digest emails."
                        tip="We will send you an email once a week with a report on your scores and how you're doing in your groups."
                    />

                    <x-form.input.checkbox
                        name="allowReminderEmails"
                        wire:model="allowReminderEmails"
                        label="Allow reminder emails."
                        tip="Let others in your group remind you to record your score. You'll never get more than one reminder email a day."
                    />

                    <div class="col-span-1 flex">
                        <x-form.input.button loading-action="accept" class="w-36">
                            Join Group
                        </x-form.input.button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layout.page-container>
