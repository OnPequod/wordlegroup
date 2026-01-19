<div {{ $attributes->merge(['class' => 'min-h-screen bg-gradient-to-b from-zinc-50 to-zinc-200']) }}>
    @if($errorMessage && count($errorMessage) > 0)
        <x-layout.flash-message type="error">{{ $errorMessage[0] }}</x-layout.flash-message>
    @elseif (session()->has('message') && session('message'))
        <x-layout.flash-message>{{ session('message') }}</x-layout.flash-message>
    @elseif(session()->has('infoMessage') && session('errorMessage'))
        <x-layout.flash-message type="info">{{ session('infoMessage') }}</x-layout.flash-message>
    @elseif(session()->has('errorMessage') && session('errorMessage'))
        <x-layout.flash-message type="error">{{ session('errorMessage') }}</x-layout.flash-message>
    @endif

    <div class="mx-auto w-full py-6 sm:py-8 px-4 sm:px-6 lg:px-10 @if($wide) max-w-6xl @else max-w-2xl @endif">

        @if($heading)
            <x-layout.heading :wide="$wide" :text-color="$headingTextColor" :heading-class="$headingClass">{{ $heading }}</x-layout.heading>

            @if($captionSlot)
                {{ $captionSlot  }}
            @elseif($captionClass)
                <div @if($captionClass) class="{{ $captionClass }}" @endif>
                    {{ $caption }}
                </div>
            @endif
        @endif

        <div class="@if($topPadding) mt-4 @endif text-gray-900">
            {{ $slot }}
        </div>
    </div>

    <x-layout.footer/>

    @push('title') {{ $title }} @endpush
</div>
