<nav x-data="{ mobileMenuOpen: false }" class="bg-gradient-to-b from-green-800 to-green-700 shadow-sm">
    <div class="px-4 mx-auto max-w-6xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            {{-- Logo --}}
            <div class="flex min-w-0">
                <a
                    href="{{ Auth::check() ? route('account.home') : route('home') }}"
                    class="group flex items-center min-w-0 text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/50 focus-visible:ring-offset-2 focus-visible:ring-offset-green-700 rounded-md"
                >
                    <span class="flex shrink-0 justify-center items-center h-8 w-8 sm:w-10 sm:h-10 font-serif text-xl sm:text-2xl font-extrabold rounded-lg shadow-sm bg-green-50 text-green-800 transition group-hover:bg-wordle-yellow group-hover:text-white">W</span>
                    <span class="px-2 sm:px-3 font-semibold font-serif text-lg sm:text-xl tracking-tight text-white truncate">
                        Wordle Group
                    </span>
                </a>
            </div>

            {{-- Desktop nav --}}
            <div class="hidden sm:flex items-center gap-1">
                <a
                    href="{{ route('about') }}"
                    class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium text-white/90 no-underline hover:bg-white/10 hover:text-white transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-green-700"
                >
                    About
                </a>
                <a
                    href="{{ route('board') }}"
                    class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium text-white/90 no-underline hover:bg-white/10 hover:text-white transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-green-700"
                >
                    Today
                </a>
                <a
                    href="{{ route('board.archive') }}"
                    class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium text-white/90 no-underline hover:bg-white/10 hover:text-white transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-green-700"
                >
                    Archive
                </a>
                <a
                    href="{{ route('leaderboard') }}"
                    class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium text-white/90 no-underline hover:bg-white/10 hover:text-white transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-green-700"
                >
                    Leaderboard
                </a>

                @guest
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium text-white/90 no-underline hover:bg-white/10 hover:text-white transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-green-700"
                    >
                        Log In
                    </a>
                    <a
                        href="{{ route('group.create') }}"
                        class="ml-1 inline-flex items-center rounded-full px-4 py-2 text-sm font-medium bg-white/90 text-green-800 no-underline hover:bg-white transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-green-700"
                    >
                        <x-icon-solid.plus class="w-4 h-4 mr-2 -ml-1"/>
                        New Group
                    </a>
                @else
                    <a
                        href="{{ route('account.home') }}"
                        class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium text-white/90 no-underline hover:bg-white/10 hover:text-white transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-green-700"
                    >
                        My Stats
                    </a>

                    {{-- My Groups dropdown --}}
                    @php
                        $userGroups = Auth::user()->load('memberships.group')->memberships->pluck('group')->sortBy('name');
                    @endphp
                    @if($userGroups->count() > 0)
                        <x-layout.dropdown
                            name="groups-dropdown"
                            width="w-56"
                            dropdown-custom="right-0"
                            button-class="inline-flex items-center gap-1 rounded-full px-4 py-2 text-sm font-medium text-white/90 no-underline hover:bg-white/10 hover:text-white transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-green-700"
                        >
                            <x-slot name="buttonSlot">
                                My Groups
                                <x-icon-regular.chevron-down class="w-3 h-3 opacity-70"/>
                            </x-slot>

                            <ul class="py-1">
                                @foreach($userGroups as $group)
                                    <li>
                                        <a
                                            class="text-sm px-3 py-2 block text-gray-600 hover:bg-gray-50"
                                            href="{{ route('group.home', $group) }}"
                                        >{{ $group->name }}</a>
                                    </li>
                                @endforeach
                                <li class="border-t border-gray-100">
                                    <a
                                        class="text-sm px-3 py-2 block text-gray-600 hover:bg-gray-50"
                                        href="{{ route('account.groups') }}"
                                    >Manage Groups</a>
                                </li>
                            </ul>
                        </x-layout.dropdown>
                    @endif

                    {{-- User dropdown --}}
                    <x-layout.dropdown
                        name="user-dropdown"
                        width="w-56"
                        dropdown-custom="right-0"
                        button-class="inline-flex items-center gap-1.5 h-10 px-2 rounded-full text-white hover:bg-white/10 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-green-700"
                    >
                        <x-slot name="buttonSlot">
                            <span class="sr-only">Open user menu</span>
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-green-50 text-green-800 font-semibold text-sm shadow-sm">
                                {{ substr($user->name, 0, 1) }}
                            </span>
                            <x-icon-regular.chevron-down class="w-4 h-4 opacity-70"/>
                        </x-slot>

                        <ul class="py-1">
                            <li class="border-gray-100 border-b">
                                <span class="text-sm px-3 py-2 block text-gray-900 font-bold">
                                    {{ $user->name }}
                                </span>
                            </li>
                            <li>
                                <a class="text-sm px-3 py-2 block text-gray-600 hover:bg-gray-50" href="{{ route('account.record-score') }}">Record Score</a>
                            </li>
                            <li>
                                <a class="text-sm px-3 py-2 block text-gray-600 hover:bg-gray-50" href="{{ route('account.home') }}">My Stats</a>
                            </li>
                            <li>
                                <a class="text-sm px-3 py-2 block text-gray-600 hover:bg-gray-50" href="{{ route('account.groups') }}">Groups</a>
                            </li>
                            <li class="border-gray-100 border-b">
                                <a class="text-sm px-3 py-2 block text-gray-600 hover:bg-gray-50" href="{{ route('account.settings') }}">Settings</a>
                            </li>
                            <li class="border-gray-100 border-b">
                                <a class="text-sm px-3 py-2 block text-gray-600 hover:bg-gray-50" href="https://www.nytimes.com/games/wordle/index.html">Play Wordle</a>
                            </li>
                            <li>
                                <a class="text-sm px-3 py-2 block text-gray-600 hover:bg-gray-50" href="{{ route('logout') }}">Log Out</a>
                            </li>
                        </ul>
                    </x-layout.dropdown>
                @endguest
            </div>

            {{-- Mobile hamburger --}}
            <div class="flex sm:hidden items-center shrink-0">
                {{-- Hamburger button --}}
                <button
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    type="button"
                    class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-white/10 transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                    aria-controls="mobile-menu"
                    :aria-expanded="mobileMenuOpen"
                >
                    <span class="sr-only">Open main menu</span>
                    {{-- Hamburger icon --}}
                    <svg x-show="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    {{-- Close icon --}}
                    <svg x-show="mobileMenuOpen" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu panel --}}
    <div
        x-show="mobileMenuOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="sm:hidden border-t border-white/10"
        id="mobile-menu"
    >
        <div class="px-4 py-3 space-y-1">
            @guest
                <a href="{{ route('login') }}" class="block rounded-md px-3 py-2 text-base font-medium text-white hover:bg-white/10">Log In</a>
                <a href="{{ route('group.create') }}" class="block rounded-md px-3 py-2 text-base font-medium text-white hover:bg-white/10">New Group</a>
                <div class="border-t border-white/10 my-2"></div>
            @else
                @php
                    $userGroups = $userGroups ?? Auth::user()->load('memberships.group')->memberships->pluck('group')->sortBy('name');
                @endphp

                <a href="{{ route('account.home') }}" class="block rounded-md px-3 py-2 text-base font-medium text-white hover:bg-white/10">My Stats</a>
                <a href="{{ route('account.record-score') }}" class="block rounded-md px-3 py-2 text-base font-medium text-white hover:bg-white/10">Record Score</a>

                @if($userGroups->count() > 0)
                    <div class="border-t border-white/10 my-2"></div>
                    <div class="px-3 py-1 text-xs font-semibold text-white/60 uppercase tracking-wider">My Groups</div>
                    @foreach($userGroups as $group)
                        <a href="{{ route('group.home', $group) }}" class="block rounded-md px-3 py-2 text-base font-medium text-white hover:bg-white/10">{{ $group->name }}</a>
                    @endforeach
                @endif

                <div class="border-t border-white/10 my-2"></div>
                <a href="{{ route('account.groups') }}" class="block rounded-md px-3 py-2 text-base font-medium text-white hover:bg-white/10">Manage Groups</a>
                <a href="{{ route('account.settings') }}" class="block rounded-md px-3 py-2 text-base font-medium text-white hover:bg-white/10">Settings</a>

                <div class="border-t border-white/10 my-2"></div>
            @endguest

            <a href="{{ route('leaderboard') }}" class="block rounded-md px-3 py-2 text-base font-medium text-white hover:bg-white/10">Leaderboard</a>
            <a href="{{ route('board') }}" class="block rounded-md px-3 py-2 text-base font-medium text-white hover:bg-white/10">Today's Puzzle</a>
            <a href="{{ route('board.archive') }}" class="block rounded-md px-3 py-2 text-base font-medium text-white hover:bg-white/10">Archive</a>
            <a href="{{ route('about') }}" class="block rounded-md px-3 py-2 text-base font-medium text-white hover:bg-white/10">About</a>
            <a href="https://www.nytimes.com/games/wordle/index.html" class="block rounded-md px-3 py-2 text-base font-medium text-white hover:bg-white/10">Play Wordle</a>

            @auth
                <div class="border-t border-white/10 my-2"></div>
                <a href="{{ route('logout') }}" class="block rounded-md px-3 py-2 text-base font-medium text-white/70 hover:bg-white/10">Log Out</a>
            @endauth
        </div>
    </div>
</nav>
