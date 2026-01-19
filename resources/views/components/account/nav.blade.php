<div>
    <div class="sm:hidden">
        <label for="tabs" class="sr-only">Select a tab</label>
        <!-- Use an "onChange" listener to redirect the user to the selected tab URL. -->
        <select
            id="tabs"
            name="tabs"
            class="block w-full rounded-md border-zinc-200 focus:border-green-700 focus:ring-green-700"
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
    <div class="hidden sm:block">
        <nav class="flex flex-wrap gap-2" aria-label="Tabs">
            @foreach($pages as $pageName => $page)
                @if(Auth::check() && $pageName === 'userGroups')
                    <x-layout.dropdown
                        label="My Groups"
                        name="userGroups"
                        width="w-72"
                        :button-class="'px-4 py-2 text-sm font-medium rounded-md transition ' . ($activePage === 'groups' ? 'bg-zinc-100 text-zinc-900' : 'text-zinc-500 hover:text-zinc-700 hover:bg-zinc-50')"
                        :chevron-text-color="$activePage === 'groups' ? 'text-white' : 'text-gray-500'"
                    >
                        <ul>
                            @foreach($user->memberships as $membership)
                                <li
                                    class="block px-4 py-2 border-b border-gray-200 last:border-0 text-gray-500 hover:bg-gray-50 last:rounded-b-md"
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
                        class="@if($activePage === $pageName) bg-zinc-100 text-zinc-900 font-medium @else text-zinc-500 hover:text-zinc-700 hover:bg-zinc-50 font-medium @endif px-3.5 py-2 text-sm rounded-md transition"
                        @if($activePage === $pageName) aria-current="page" @endif
                    > {{ $page['title'] }} </a>
                @endif
            @endforeach
        </nav>
    </div>
</div>
