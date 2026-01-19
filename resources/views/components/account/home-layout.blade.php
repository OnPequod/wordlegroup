<div class="mx-auto w-full max-w-6xl">
    <div class="pt-1 pb-6">
        <x-account.nav :active-page="$page" />
    </div>

    <div class="flex-grow">
        {{ $slot }}
    </div>
</div>
