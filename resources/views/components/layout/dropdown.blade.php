
<div class="flex justify-center">
    <div
        x-data="{
            open: false,
            toggle() {
                if (this.open) {
                    return this.close()
                }

                this.open = true
            },
            close(focusAfter) {
                if (! this.open) return

                this.open = false

                focusAfter && focusAfter.focus()
            }
        }"
        x-on:keydown.escape.prevent.stop="close($refs.button)"
        x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
        x-id="['{{ $name }}']"
        class="relative"
    >
        <!-- Button -->
        <button
            x-ref="button"
            x-on:click="toggle()"
            :aria-expanded="open"
            :aria-controls="$id('{{ $name }}')"
            type="button"
            class="{{ $buttonClass }} inline-flex items-center"
        >
            @if($buttonSlot)
                {{ $buttonSlot }}
            @else
            <span>{{ $label }}</span>
            <x-icon-regular.chevron-down x-show="! open" :class="'ml-2 h-3 w-3 ' . $chevronTextColor" />
            <x-icon-regular.chevron-up x-show="open" x-cloak :class="'ml-2 h-3 w-3 ' . $chevronTextColor" />
            @endif
        </button>

        <!-- Panel -->
        <div
            x-ref="panel"
            x-show="open"
            x-on:click.outside="close($refs.button)"
            :id="$id('{{ $name }}')"
            style="display: none;"
            class="z-50 @if($dropdownCustom) {{ $dropdownCustom }} @elseif($alignDropdown === 'left') left-0 @elseif($alignDropdown === 'left') right-0 @else left-1/2 transform -translate-x-1/2 @endif absolute mt-2 rounded-md shadow-lg bg-white ring-1 ring-black/5 focus:outline-none {{$width}}"
            role="menu"
            aria-orientation="vertical"
            aria-labelledby="{{ $name }}"
            tabindex="-1"
        >
            {{ $slot }}
        </div>
    </div>
</div>
