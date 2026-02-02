<!-- This example requires Tailwind CSS v2.0+ -->
<nav class="bg-gradient-to-b from-green-800 to-green-700 shadow-sm">
    <div class="px-4 mx-auto max-w-6xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <a
                    href="{{ Auth::check() ? route('account.home') : route('home') }}"
                    class="group flex flex-shrink-0 items-center text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/50 focus-visible:ring-offset-2 focus-visible:ring-offset-green-700 rounded-md"
                >
                    <span
                        class="flex justify-center items-center h-8 w-8 sm:w-10 sm:h-10 font-serif text-xl sm:text-2xl font-extrabold rounded-lg shadow-sm bg-green-50 text-green-800 transition group-hover:bg-wordle-yellow group-hover:text-white"
                    >W</span>
                    <span
                        class="px-2 sm:px-3 font-semibold font-serif text-lg sm:text-xl tracking-tight text-white"
                    >
                        Wordle Group
                    </span>
                </a>
                {{--                <div class="hidden md:ml-6 md:flex md:items-center md:space-x-4">--}}
                {{--                    <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->--}}
                {{--                    <a href="#" class="px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-md" aria-current="page">Dashboard</a>--}}

                {{--                    <a href="#" class="px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-700 hover:text-white">Team</a>--}}

                {{--                    <a href="#" class="px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-700 hover:text-white">Projects</a>--}}

                {{--                    <a href="#" class="px-3 py-2 text-sm font-medium text-gray-300 rounded-md hover:bg-gray-700 hover:text-white">Calendar</a>--}}
                {{--                </div>--}}
            </div>
            <div class="flex items-center">
                <div class="flex items-center flex-shrink-0">
                    <a
                        href="{{ route('about') }}"
                        class="hidden sm:inline-flex items-center rounded-full px-4 py-2 text-sm font-medium text-white/90 no-underline hover:bg-white/10 hover:text-white transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-green-700"
                    >
                        About
                    </a>
                    <a
                        href="{{ route('board') }}"
                        class="hidden sm:inline-flex items-center rounded-full px-4 py-2 text-sm font-medium text-white/90 no-underline hover:bg-white/10 hover:text-white transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-green-700"
                    >
                        Today
                    </a>
                    <a
                        href="{{ route('board.archive') }}"
                        class="hidden sm:inline-flex items-center rounded-full px-4 py-2 text-sm font-medium text-white/90 no-underline hover:bg-white/10 hover:text-white transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-green-700"
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
                            <span class="hidden sm:inline">New Group</span>
                            <span class="sm:hidden inline">Group</span>
                        </a>
                    @endif
                </div>
                @if(Auth::check())
                    <div class="ml-4 md:flex-shrink-0 flex items-center">

                        <div class="relative ml-3">
                            <div class="flex items-center gap-1">
                                {{-- My Stats nav item --}}
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
                                        button-class="hidden sm:inline-flex items-center gap-1 rounded-full px-4 py-2 text-sm font-medium text-white/90 no-underline hover:bg-white/10 hover:text-white transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:ring-offset-2 focus-visible:ring-offset-green-700"
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

                                {{-- User dropdown trigger with chevron --}}
                                <x-layout.dropdown
                                    name="user-dropdown"
                                    width="w-56"
                                    dropdown-custom="right-0 sm:left-1/2 sm:transform sm:-translate-x-1/2"
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
                                        <li class="border-gray-100 border-b last:border-b-0">
                                            <span class="text-sm px-3 py-2 block text-gray-900 font-bold">
                                                {{ $user->name }}
                                            </span>
                                        </li>
                                        <li class="border-gray-100 border-b last:border-b-0">
                                            <a
                                                class="text-sm px-3 py-2 block text-gray-600 hover:bg-gray-50"
                                                href="{{ route('account.record-score') }}"
                                            >Record Score</a>
                                        </li>
                                        <li>
                                            <a
                                                class="text-sm px-3 py-2 block text-gray-600 hover:bg-gray-50"
                                                href="{{ route('account.home') }}"
                                            >My Stats</a>
                                        </li>
                                        <li>
                                            <a
                                                class="text-sm px-3 py-2 block text-gray-600 hover:bg-gray-50"
                                                href="{{ route('account.groups') }}"
                                            >Groups</a>
                                        </li>
                                        <li class="border-gray-100 border-b last:border-b-0">
                                            <a
                                                class="text-sm px-3 py-2 block text-gray-600 hover:bg-gray-50"
                                                href="{{ route('account.settings') }}"
                                            >Settings</a>
                                        </li>
                                        <li class="border-gray-100 border-b last:border-b-0">
                                            <a
                                                class="text-sm px-3 py-2 block text-gray-600 hover:bg-gray-50"
                                                href="https://www.nytimes.com/games/wordle/index.html"
                                            >Play Wordle</a>
                                        </li>
                                        <li class="">
                                            <a
                                                class="text-sm px-3 py-2 block text-gray-600 hover:bg-gray-50"
                                                href="{{ route('logout') }}"
                                            >Log Out</a>
                                        </li>
                                    </ul>
                                </x-layout.dropdown>
                            </div>

                        </div>
                    </div>
            </div>
            @endif
        </div>
    </div>


</nav>
