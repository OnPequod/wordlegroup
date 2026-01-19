<footer class="w-full bg-white/80 border-t border-zinc-200 mt-16">
    <div class="max-w-5xl mx-auto px-6 py-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            {{-- Primary actions --}}
            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-x-6 gap-y-2">
                <a
                    href="{{ route('group.create') }}"
                    class="text-sm font-medium text-zinc-700 hover:text-green-700 hover:underline underline-offset-4 decoration-green-600 transition"
                >
                    Create Group
                </a>
                @if(! Auth::check())
                    <a
                        href="{{ route('login') }}"
                        class="text-sm font-medium text-zinc-700 hover:text-green-700 hover:underline underline-offset-4 decoration-green-600 transition"
                    >
                        Log In
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="text-sm font-medium text-zinc-700 hover:text-green-700 hover:underline underline-offset-4 decoration-green-600 transition"
                    >
                        Register
                    </a>
                @else
                    <a
                        href="{{ route('logout') }}"
                        class="text-sm font-medium text-zinc-700 hover:text-green-700 hover:underline underline-offset-4 decoration-green-600 transition"
                    >
                        Log Out
                    </a>
                @endif
            </div>

            {{-- Secondary links --}}
            <div class="flex flex-wrap items-center justify-center sm:justify-end gap-x-6 gap-y-2">
                <a
                    href="{{ route('about') }}"
                    class="text-sm font-medium text-zinc-600 hover:text-green-700 hover:underline underline-offset-4 decoration-green-600 transition"
                >
                    About
                </a>
                <a
                    href="{{ route('rules-and-faq') }}"
                    class="text-sm font-medium text-zinc-600 hover:text-green-700 hover:underline underline-offset-4 decoration-green-600 transition"
                >
                    Rules/FAQ
                </a>
                <a
                    href="{{ route('contact') }}"
                    class="text-sm font-medium text-zinc-600 hover:text-green-700 hover:underline underline-offset-4 decoration-green-600 transition"
                >
                    Contact
                </a>
                <a
                    href="{{ route('privacy-policy') }}"
                    class="text-sm font-medium text-zinc-600 hover:text-green-700 hover:underline underline-offset-4 decoration-green-600 transition"
                >
                    Privacy Policy
                </a>
            </div>
        </div>
    </div>
</footer>
