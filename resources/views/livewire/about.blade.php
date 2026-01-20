<x-layout.page-container title="About Wordle Group" heading="About Wordle Group">
    <x-layout.social-meta
        title="About Wordle Group"
        :url="route('about')"
        description="About Wordle Group, a website created to to keep score with your friends in Wordle."
    />
    <div class="prose tracking-tight max-w-none rounded-2xl border border-gray-100 bg-white p-6 sm:p-8 shadow-[0_10px_30px_var(--color-shadow)]">
        <p>
            Wordle Group was created by <a href="mailto:erik@pequod.sh">Erik Westlund</a> to provide an easy way to see who in our family group chat was doing the best (hint: it's not me).
        </p>
        <p>
            Wordle Group is not affiliated in any way with <a href="https://www.nytimes.com/games/wordle/">Wordle</a>, or its owner, <a href="https://www.nytimes.com/">The New York Times</a>. <strong>Please do not contact me with Wordle-related inquiries</strong> — I cannot help with issues related to the game itself.
        </p>
        <p>
            If you have questions about how Wordle Group works or the rules applied when keeping track of Wordle scores within a group, visit the <a href="{{ route('rules-and-faq') }}">Rules and Frequency Asked Questions page</a>.
        </p>
        <p>
            Wordle Group was built using <a href="https://laravel.com/">Laravel</a>.
        </p>
        <p>
            If you are interested in hiring me to help build your website, contact me at <a href="mailto:erik@pequod.sh">erik@pequod.sh</a>.
        </p>
    </div>
</x-layout.page-container>
