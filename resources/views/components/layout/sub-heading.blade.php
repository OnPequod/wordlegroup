<h2 {{ $attributes->merge(['class' => 'text-base font-semibold text-zinc-900 ' . ($textColor ?: '')]) }}>
    {{ $slot }}
</h2>
