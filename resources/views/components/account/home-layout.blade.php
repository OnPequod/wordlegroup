<div class="mx-auto w-full max-w-6xl">
    <div class="pt-1 pb-8">
        <x-account.nav :active-page="$page" />
    </div>

    <div class="flex-grow">
        {{ $slot }}
    </div>
</div>
