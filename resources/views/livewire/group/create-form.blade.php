<form wire:submit.prevent="store" class="my-0">
    <div class="py-8 grid grid-cols-1 gap-y-7">
        <x-form.input.text
            wire:model.blur="groupName"
            :autofocus="$autofocus"
            name="groupName"
            :errors="$errors"
            label="Group Name"
            placeholder="My Group"
        />
        @unless(Auth::check())
            <x-form.input.text
                wire:model.blur="email"
                name="email"
                :errors="$errors"
                type="email"
                label="Email Address"
                placeholder="my@email.com"
                tip="Make sure you use the email account you will send your scores from."
            />
            <x-form.input.text
                wire:model="userName" name="groupName" :errors="$errors" label="Your Name" placeholder="Your Name"
            />
        @endunless
        @unless(Auth::check())
        <x-form.input.checkbox
            name="userPublicProfile"
            wire:model="userPublicProfile"
            label="Make my profile public."
            tip="We will create you a user account along with your group. Click here to allow others who are not members of your groups to see your scores."
        />
        @endunless
        <x-form.input.checkbox
            name="public"
            wire:model="public"
            label="Make this group public."
            tip="This will allow non-group members to see the group page. Users who opt to keep their profiles private can still hide their names."
        />
    </div>
    <x-form.input.button loading-action="store" class="w-24">Create</x-form.input.button>
</form>
