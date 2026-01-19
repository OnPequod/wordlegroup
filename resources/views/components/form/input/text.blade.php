<div>
    <div
        class="relative rounded-md border @if($errors->has($name)) border-red-600 @else border-zinc-300 @endif bg-white px-4 py-3 focus-within:ring-2 focus-within:ring-green-700/20 focus-within:border-green-700"    >
        @if($label)
            <label
                for="name"
                class="absolute -top-2 left-3 -mt-px inline-block px-1 bg-white text-[11px] font-semibold text-zinc-600"
            >{{ $label }}</label>
        @endif
        <input
            {{ $attributes->merge([
                'class' => 'block w-full border-0 bg-transparent px-0 pt-2 text-sm text-zinc-900 placeholder-zinc-400 focus:ring-0',
                'type' => $attributes->get('type') ?? 'text' ,
                'name' => $name,
                'id' => $name,
                'placeholder' => $placeholder
            ]) }}
        >
    </div>
    @if($tip)
        <p class="mt-1.5 text-xs text-zinc-500">{{ $tip }}</p>
    @endif
    @error($name)
    <div class="text-red-600 text-sm mt-1">{!! $message !!}</div>
    @enderror
</div>
