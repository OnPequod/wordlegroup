<div>
    {{-- Sticky input at top --}}
    @if($this->canPost())
        <div class="sticky top-0 z-10 px-4 py-3 bg-white border-b border-zinc-100">
            <form wire:submit.prevent="createPost" class="flex gap-2">
                <input
                    wire:model="body"
                    type="text"
                    class="flex-1 rounded-full border border-zinc-300 px-4 py-2 text-sm focus:border-green-600 focus:ring-green-600"
                    placeholder="Write a message..."
                />
                <button
                    type="submit"
                    class="inline-flex items-center justify-center w-10 h-10 text-white bg-green-700 rounded-full hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                        <path d="M3.105 2.289a.75.75 0 00-.826.95l1.414 4.925A1.5 1.5 0 005.135 9.25h6.115a.75.75 0 010 1.5H5.135a1.5 1.5 0 00-1.442 1.086l-1.414 4.926a.75.75 0 00.826.95 28.896 28.896 0 0015.293-7.154.75.75 0 000-1.115A28.897 28.897 0 003.105 2.289z" />
                    </svg>
                </button>
            </form>
            @error('body')
                <p class="mt-1 px-4 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    @endif

    {{-- Messages container --}}
    <div class="max-h-[500px] overflow-y-auto">
        @if($this->posts->isEmpty())
            <div class="px-6 py-8 text-center">
                <p class="text-sm text-zinc-500">No discussions yet. Be the first to start one!</p>
            </div>
        @else
            <div class="px-4 py-3 space-y-4">
                @foreach($this->groupedPosts as $dayData)
                    {{-- Day separator --}}
                    <div class="flex items-center gap-3 py-2">
                        <div class="flex-1 h-px bg-zinc-200"></div>
                        <span class="text-xs font-medium text-zinc-400">
                            @php
                                $date = \Carbon\Carbon::parse($dayData['date']);
                                if ($date->isToday()) {
                                    echo 'Today';
                                } elseif ($date->isYesterday()) {
                                    echo 'Yesterday';
                                } else {
                                    echo $date->format('F j, Y');
                                }
                            @endphp
                        </span>
                        <div class="flex-1 h-px bg-zinc-200"></div>
                    </div>

                    {{-- Message groups for this day --}}
                    @foreach($dayData['groups'] as $group)
                        <div class="flex items-start gap-3 {{ $group['first_post_at']->lt(now()->subDay()) ? 'opacity-60' : '' }}" wire:key="group-{{ $group['user_id'] }}-{{ $group['first_post_at']->timestamp }}">
                            {{-- Avatar (shown once per group) --}}
                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-green-100 text-green-800 flex items-center justify-center font-semibold text-xs">
                                {{ substr($group['user']->name, 0, 1) }}
                            </div>

                            {{-- Messages from this user --}}
                            <div class="flex-1 min-w-0 space-y-1">
                                {{-- User name and time (shown once) --}}
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-zinc-900">{{ $group['user']->name }}</span>
                                    <span class="text-xs text-zinc-400">{{ $group['first_post_at']->format('g:i A') }}</span>
                                </div>

                                {{-- All messages from this user in this group --}}
                                @foreach($group['posts'] as $post)
                                    <div wire:key="post-{{ $post->id }}">
                                        @if($editingPostId === $post->id)
                                            <form wire:submit.prevent="saveEdit" class="mt-1">
                                                <textarea
                                                    wire:model="editBody"
                                                    rows="2"
                                                    class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:border-green-600 focus:ring-green-600"
                                                ></textarea>
                                                @error('editBody')
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                                <div class="mt-2 flex gap-2">
                                                    <button
                                                        type="submit"
                                                        class="px-3 py-1 text-xs font-medium text-white bg-green-700 rounded hover:bg-green-800"
                                                    >
                                                        Save
                                                    </button>
                                                    <button
                                                        type="button"
                                                        wire:click="cancelEditing"
                                                        class="px-3 py-1 text-xs font-medium text-zinc-600 bg-zinc-100 rounded hover:bg-zinc-200"
                                                    >
                                                        Cancel
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <p class="text-sm text-zinc-700 whitespace-pre-wrap">{{ $post->body }}</p>

                                            {{-- Actions (only show on hover via group) --}}
                                            <div class="flex items-center gap-3 mt-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                                @if($this->canPost())
                                                    <button
                                                        type="button"
                                                        wire:click="startReplying({{ $post->id }})"
                                                        class="text-xs text-zinc-400 hover:text-zinc-600"
                                                    >
                                                        Reply
                                                    </button>
                                                @endif
                                                @if($post->canBeEditedBy(Auth::user()))
                                                    <button
                                                        type="button"
                                                        wire:click="startEditing({{ $post->id }})"
                                                        class="text-xs text-zinc-400 hover:text-zinc-600"
                                                    >
                                                        Edit
                                                    </button>
                                                @endif
                                                @if($post->canBeDeletedBy(Auth::user()))
                                                    <button
                                                        type="button"
                                                        wire:click="deletePost({{ $post->id }})"
                                                        wire:confirm="Are you sure you want to delete this message?"
                                                        class="text-xs text-red-400 hover:text-red-600"
                                                    >
                                                        Delete
                                                    </button>
                                                @endif
                                            </div>

                                            {{-- Reply form --}}
                                            @if($replyingToPostId === $post->id)
                                                <form wire:submit.prevent="saveReply" class="mt-2 pl-3 border-l-2 border-zinc-200">
                                                    <textarea
                                                        wire:model="replyBody"
                                                        rows="2"
                                                        class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:border-green-600 focus:ring-green-600"
                                                        placeholder="Write a reply..."
                                                    ></textarea>
                                                    @error('replyBody')
                                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                    <div class="mt-2 flex gap-2">
                                                        <button
                                                            type="submit"
                                                            class="px-3 py-1 text-xs font-medium text-white bg-green-700 rounded hover:bg-green-800"
                                                        >
                                                            Reply
                                                        </button>
                                                        <button
                                                            type="button"
                                                            wire:click="cancelReplying"
                                                            class="px-3 py-1 text-xs font-medium text-zinc-600 bg-zinc-100 rounded hover:bg-zinc-200"
                                                        >
                                                            Cancel
                                                        </button>
                                                    </div>
                                                </form>
                                            @endif

                                            {{-- Replies --}}
                                            @if($post->replies->isNotEmpty())
                                                <div class="mt-2 space-y-2 pl-3 border-l-2 border-zinc-200">
                                                    @foreach($post->replies as $reply)
                                                        <div wire:key="reply-{{ $reply->id }}" class="flex items-start gap-2">
                                                            <div class="flex-shrink-0 h-5 w-5 rounded-full bg-zinc-100 text-zinc-600 flex items-center justify-center font-semibold text-[10px]">
                                                                {{ substr($reply->user->name, 0, 1) }}
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <div class="flex items-center gap-2">
                                                                    <span class="text-xs font-medium text-zinc-900">{{ $reply->user->name }}</span>
                                                                    <span class="text-xs text-zinc-400">{{ $reply->created_at->format('g:i A') }}</span>
                                                                </div>

                                                                @if($editingPostId === $reply->id)
                                                                    <form wire:submit.prevent="saveEdit" class="mt-1">
                                                                        <textarea
                                                                            wire:model="editBody"
                                                                            rows="2"
                                                                            class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:border-green-600 focus:ring-green-600"
                                                                        ></textarea>
                                                                        <div class="mt-2 flex gap-2">
                                                                            <button
                                                                                type="submit"
                                                                                class="px-3 py-1 text-xs font-medium text-white bg-green-700 rounded hover:bg-green-800"
                                                                            >
                                                                                Save
                                                                            </button>
                                                                            <button
                                                                                type="button"
                                                                                wire:click="cancelEditing"
                                                                                class="px-3 py-1 text-xs font-medium text-zinc-600 bg-zinc-100 rounded hover:bg-zinc-200"
                                                                            >
                                                                                Cancel
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                @else
                                                                    <p class="mt-0.5 text-sm text-zinc-700 whitespace-pre-wrap">{{ $reply->body }}</p>
                                                                    <div class="mt-0.5 flex items-center gap-3">
                                                                        @if($reply->canBeEditedBy(Auth::user()))
                                                                            <button
                                                                                type="button"
                                                                                wire:click="startEditing({{ $reply->id }})"
                                                                                class="text-xs text-zinc-400 hover:text-zinc-600"
                                                                            >
                                                                                Edit
                                                                            </button>
                                                                        @endif
                                                                        @if($reply->canBeDeletedBy(Auth::user()))
                                                                            <button
                                                                                type="button"
                                                                                wire:click="deletePost({{ $reply->id }})"
                                                                                wire:confirm="Are you sure you want to delete this reply?"
                                                                                class="text-xs text-red-400 hover:text-red-600"
                                                                            >
                                                                                Delete
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>

            {{-- Show earlier messages button --}}
            @if($this->hasEarlierMessages && !$showingEarlier)
                <div class="px-4 py-3 text-center">
                    <button
                        type="button"
                        wire:click="showEarlierMessages"
                        class="text-sm text-green-700 hover:text-green-800 font-medium"
                    >
                        Show older messages
                    </button>
                </div>
            @endif
        @endif
    </div>
</div>
