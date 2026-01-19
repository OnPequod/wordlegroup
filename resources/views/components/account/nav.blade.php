<div>
    {{-- Mobile: dropdown select --}}
    <div class="sm:hidden">
        <label for="tabs" class="sr-only">Select a tab</label>
        <select
            id="tabs"
            name="tabs"
            class="block w-full rounded-full border-zinc-200 bg-white py-2 pl-4 pr-10 text-sm font-medium text-zinc-900 focus:border-green-600 focus:ring-green-600"
            x-data="{selected: '{{ $activePage }}', routeMap: {{ json_encode($routeMap, JSON_HEX_APOS) }} }"
            x-on:change="Turbo.visit(routeMap[$event.target.value])"
        >
            @foreach($pages as $pageName => $page)
                @if(isset($page['placeholder']) && $page['placeholder'])
                    <option
                        value="{{ $pageName }}" {{ $pageName === 'placeholder' ? ' disabled selected' : '' }}
                    >{{ $page['title'] }}</option>
                @elseif($pageName === 'userGroups')

                @else
                    <option
                        value="{{ $pageName }}" {{ $activePage === $pageName ? ' selected' : '' }}>{{ $page['title'] }}</option>
                @endif
            @endforeach
            @if(Auth::check())
            <optgroup label="Group Pages">
                @foreach($user->memberships as $membership)
                    <option
                        value="group.{{ $membership->group_id }}" {{ $activePage === "group.{$membership->group_id}" ? ' selected' : '' }}>{{ $membership->group->name }}</option>
                @endforeach
            </optgroup>
            @endif
        </select>
    </div>

    {{-- Desktop: pill segmented control --}}
    <div class="hidden sm:flex sm:justify-center">
        <nav
            class="inline-flex items-center h-11 rounded-full bg-zinc-200/50 p-1 gap-1"
            aria-label="Tabs"
        >
            @foreach($pages as $pageName => $page)
                @if(Auth::check() && $pageName === 'userGroups')
                    <x-layout.dropdown
                        label="My Groups"
                        name="userGroups"
                        width="w-72"
                        :button-class="'inline-flex items-center justify-center gap-2 h-9 rounded-full px-5 text-sm leading-none transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-green-600 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-100 ' . (str_starts_with($activePage, 'group') ? 'bg-white text-zinc-900 font-semibold shadow-sm' : 'text-zinc-600 font-medium hover:bg-white/70 hover:text-zinc-900')"
                        chevron-class="size-4 opacity-60"
                    >
                        <ul>
                            @foreach($user->memberships as $membership)
                                <li
                                    class="block px-5 py-4 border-b border-zinc-100 last:border-0 text-zinc-600 hover:bg-zinc-50 first:rounded-t-md last:rounded-b-md"
                                >
                                    <x-group.dropdown-list-item :group-membership="$membership" />
                                </li>
                            @endforeach
                        </ul>
                    </x-layout.dropdown>
                @elseif(in_array($pageName, ['groups', 'placeholder']))

                @else
                    <a
                        href="{{ $page['route'] }}"
                        @class([
                            'inline-flex items-center justify-center h-9 rounded-full px-5 text-sm leading-none transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-green-600 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-100',
                            'bg-white text-zinc-900 font-semibold shadow-sm' => $activePage === $pageName,
                            'text-zinc-600 font-medium hover:bg-white/70 hover:text-zinc-900' => $activePage !== $pageName,
                        ])
                        @if($activePage === $pageName) aria-current="page" @endif
                    >{{ $page['title'] }}</a>
                @endif
            @endforeach
        </nav>
    </div>
</div>
