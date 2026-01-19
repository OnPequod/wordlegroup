<div {{ $attributes }}>
    <div class="text-center py-2">
        <h1 class="@if($headingClass) {{ $headingClass }} @else text-2xl sm:text-3xl font-serif font-bold tracking-tight text-zinc-900 @endif {{ $textColor ?? '' }}">{{ $slot }}</h1>
    </div>
</div>
