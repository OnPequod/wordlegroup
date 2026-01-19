<button
    @if($primary)
        {{ $attributes->merge([
            'class' => 'justify-center px-4 py-2 min-h-10 border border-transparent rounded-md text-white bg-green-700 hover:bg-green-800 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-700'
        ]) }}
    @else
        {{ $attributes->merge([
              'class' => 'justify-center px-4 py-2 min-h-10 border border-zinc-200 hover:border-zinc-300 rounded-md text-zinc-700 bg-white hover:bg-zinc-50 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-700'
          ]) }}
    @endif
>
    <span
        class="inline-flex items-center"
        @if($loadingAction)
        wire:loading.remove
        wire:target="{{ $loadingAction }}"
        @endif
    >
        {{ $slot }}
    </span>
    <span
        class="inline-flex items-center"
        wire:loading
        @if($loadingAction)wire:target="{{ $loadingAction }}"@endif
    >
        <x-layout.loading-spinner />
    </span>
</button>
