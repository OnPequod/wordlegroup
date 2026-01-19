
<ul class="flex flex-wrap items-center justify-center gap-4 text-xs text-gray-500">
    <li class="text-xs link"><a href="{{ route('group.create') }}">Create Group</a>
    @if(! Auth::check())
        <li class="text-xs link"><a href="{{ route('login') }}">Log In</a>
        <li class="text-xs link"><a href="{{ route('register') }}">Register</a>
        </li>
    @else
        <li class="text-xs link"><a href="{{ route('logout') }}">Log out</a></li>
    @endif
</ul>

<ul class="mt-4 flex flex-wrap items-center justify-center gap-4 text-xs text-gray-500">
    <li class="text-xs link"><a href="{{ route('about') }}">About</a>
    <li class="text-xs link"><a href="{{ route('rules-and-faq') }}">Rules/FAQ</a>
    <li class="text-xs link"><a href="{{ route('contact') }}">Contact</a>
    <li class="text-xs link"><a href="{{ route('privacy-policy') }}">Privacy Policy</a>
</ul>
