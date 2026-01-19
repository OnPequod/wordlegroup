<div {{ $attributes }}>
    @include('scripts.copy-to-clipboard')
    <div x-data class="flex flex-wrap items-center justify-center gap-4 py-1">
        <button
            @click="copyToClipboard('{{ route('account.profile', $user) }}')"
            type="button"
            class="inline-flex items-center gap-2 rounded-md border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50"
        >
            <x-icon-solid.link class="h-4 w-4 text-zinc-500"/>
            Copy link
        </button>
        <a
            href="mailto:?subject=View my Wordle Stats on Wordle Group&body=View my Wordle Stats on Wordle Group at {{ urlencode(route('account.profile', $user)) }}."
            class="inline-flex items-center gap-2 rounded-md border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50"
        >
            <x-icon-solid.envelope class="h-4 w-4 text-zinc-500"/>
            Email
        </a>
        <button
            @click="window.open('https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('account.profile', $user)) }}', '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=600')"
            type="button"
            class="inline-flex items-center gap-2 rounded-md border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50"
        >
            <x-icon-brands.facebook-f class="h-4 w-4 text-zinc-500"/>
            Facebook
        </button>
        <button
            @click="window.open('https://twitter.com/share?url={{ urlencode(route('account.profile', $user)) }}&via=wordlegroup&text={{ urlencode('View my Wordle Stats on Wordle Group') }}.', '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=600')"
            type="button"
            class="inline-flex items-center gap-2 rounded-md border border-zinc-200 bg-white px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50"
        >
            <x-icon-brands.twitter class="h-4 w-4 text-zinc-500"/>
            Twitter
        </button>
    </div>
</div>
