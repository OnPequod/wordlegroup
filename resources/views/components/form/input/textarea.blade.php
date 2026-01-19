<div>
    <div
        class="relative rounded-md border @if($errors->has($name)) border-red-600 @else border-zinc-300 @endif bg-white px-4 py-3 focus-within:ring-2 focus-within:ring-green-700/20 focus-within:border-green-700"
    >
        @if($label)
            <label
                for="{{ $name }}"
                class="absolute -top-2 left-3 -mt-px inline-block px-1 bg-white text-[11px] font-semibold text-zinc-600"
            >{{ $label }}</label>
        @endif
        <textarea
            {{ $attributes->merge([
                'name' => $name,
                'id' => $name,
                'placeholder' => $placeholder,
                'class' => 'block w-full border-0 bg-transparent px-0 pt-2 text-sm text-zinc-900 placeholder-zinc-400 focus:ring-0'
            ]) }}
            rows="{{ $rows }}"
        ></textarea>
    </div>
    @if($tip)
        <p class="mt-2 text-xs text-zinc-500">{{ $tip }}</p>
    @endif
    @error($name)
        <div class="text-red-600 text-sm mt-1">{!! $message !!}</div>
    @enderror
</div>


{{--<div>--}}
{{--    @if($label)--}}
{{--        <label for="about" class="block text-sm font-semibold text-gray-700 sm:mt-px sm:pt-2">{{ $label }}</label>--}}
{{--    @endif--}}
{{--    <div class="mt-1 sm:mt-1 sm:col-span-2">--}}
{{--        <textarea--}}
{{--            {{ $attributes->merge([--}}
{{--                'name' => $name,--}}
{{--                 'id' => $name,--}}
{{--                 'placeholder' => $placeholder,--}}
{{--                 'class' => 'max-w-xl shadow-sm block w-full focus:ring-green-700 focus:border-green-500 sm:text-sm border placeholder-gray-400 rounded-md ' . ($errors->has($name) ? 'border-red-600' : 'border-gray-300')--}}
{{--               ]) }}--}}
{{--            id="about"--}}
{{--            name="about"--}}
{{--            rows="{{ $rows }}"--}}
{{--        ></textarea>--}}
{{--        @if($tip)--}}
{{--            <p class="mt-2 text-sm text-gray-500">{{ $tip }}</p>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--    @error($name)--}}
{{--    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>--}}
{{--    @enderror--}}
{{--</div>--}}
