<div class="flex flex-wrap justify-center items-center gap-y-2 text-sm">
    <a href="{{ route('board') }}" class="px-3 py-1 rounded-full text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 transition">Today's Puzzle</a>
    <span class="text-zinc-400">&middot;</span>
    <a href="{{ route('board.archive') }}" class="px-3 py-1 rounded-full text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 transition">Archive</a>
    <span class="text-zinc-400">&middot;</span>
    <a href="{{ route('leaderboard') }}" class="px-3 py-1 rounded-full text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 transition">Leaderboard</a>
    <span class="text-zinc-400">&middot;</span>
    <a href="{{ route('group.create') }}" class="px-3 py-1 rounded-full text-zinc-500 hover:text-zinc-900 hover:bg-zinc-100 transition">Create Group</a>
</div>
