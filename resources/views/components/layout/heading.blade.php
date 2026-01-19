<div {{ $attributes }}>
    <div class="text-center">
        <h1 class="@if($headingClass) {{ $headingClass }} @else text-2xl sm:text-3xl font-semibold text-zinc-900 @endif {{ $textColor ?? '' }}">{{ $slot }}</h1>
    </div>
</div>
