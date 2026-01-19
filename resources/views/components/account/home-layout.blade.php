<div class="mx-auto w-full max-w-6xl">
    <div class="flex justify-center">
        <x-account.nav :active-page="$page" />
    </div>

    <x-layout.hr class="my-5" />

    <div class="flex-grow">
        {{ $slot }}
    </div>
</div>
