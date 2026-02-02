<div class="mt-8">
    <h3 class="text-lg font-semibold text-zinc-900 mb-4">Comments</h3>

    @if($this->canComment())
        <form wire:submit.prevent="addComment" class="mb-6">
            <textarea
                wire:model="body"
                rows="3"
                class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:border-green-600 focus:ring-green-600"
                placeholder="Add a comment..."
            ></textarea>
            @error('body')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <div class="mt-2 flex justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-700 rounded-lg hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2"
                >
                    Post Comment
                </button>
            </div>
        </form>
    @elseif(!Auth::check())
        <p class="text-sm text-zinc-500 mb-6">
            <a href="{{ route('login') }}" class="text-green-700 hover:underline">Log in</a> to comment.
        </p>
    @endif

    @if($this->comments->isEmpty())
        <p class="text-sm text-zinc-500">No comments yet.</p>
    @else
        <div class="space-y-4">
            @foreach($this->comments as $comment)
                <div class="rounded-lg border border-zinc-200 bg-white p-4" wire:key="comment-{{ $comment->id }}">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-green-100 text-green-800 flex items-center justify-center font-semibold text-sm">
                            {{ substr($comment->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-zinc-900">{{ $comment->user->name }}</span>
                                <span class="text-xs text-zinc-500">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>

                            @if($editingCommentId === $comment->id)
                                <form wire:submit.prevent="saveEdit" class="mt-2">
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
                                <p class="mt-1 text-sm text-zinc-700">{{ $comment->body }}</p>

                                <div class="mt-2 flex items-center gap-3">
                                    @if($this->canComment())
                                        <button
                                            type="button"
                                            wire:click="startReplying({{ $comment->id }})"
                                            class="text-xs text-zinc-500 hover:text-zinc-700"
                                        >
                                            Reply
                                        </button>
                                    @endif
                                    @if($comment->canBeEditedBy(Auth::user()))
                                        <button
                                            type="button"
                                            wire:click="startEditing({{ $comment->id }})"
                                            class="text-xs text-zinc-500 hover:text-zinc-700"
                                        >
                                            Edit
                                        </button>
                                    @endif
                                    @if($comment->canBeDeletedBy(Auth::user()))
                                        <button
                                            type="button"
                                            wire:click="deleteComment({{ $comment->id }})"
                                            wire:confirm="Are you sure you want to delete this comment?"
                                            class="text-xs text-red-500 hover:text-red-700"
                                        >
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            @endif

                            {{-- Reply form --}}
                            @if($replyingToCommentId === $comment->id)
                                <form wire:submit.prevent="saveReply" class="mt-3 pl-4 border-l-2 border-zinc-200">
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
                            @if($comment->replies->isNotEmpty())
                                <div class="mt-4 pt-4 space-y-4 pl-4 border-l-2 border-zinc-200">
                                    @foreach($comment->replies as $reply)
                                        <div wire:key="reply-{{ $reply->id }}">
                                            <div class="flex items-start gap-2">
                                                <div class="flex-shrink-0 h-6 w-6 rounded-full bg-zinc-100 text-zinc-600 flex items-center justify-center font-semibold text-xs">
                                                    {{ substr($reply->user->name, 0, 1) }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs font-medium text-zinc-900">{{ $reply->user->name }}</span>
                                                        <span class="text-xs text-zinc-500">{{ $reply->created_at->diffForHumans() }}</span>
                                                    </div>

                                                    @if($editingCommentId === $reply->id)
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
                                                        <p class="mt-0.5 text-sm text-zinc-700">{{ $reply->body }}</p>
                                                        <div class="mt-1 flex items-center gap-3">
                                                            @if($reply->canBeEditedBy(Auth::user()))
                                                                <button
                                                                    type="button"
                                                                    wire:click="startEditing({{ $reply->id }})"
                                                                    class="text-xs text-zinc-500 hover:text-zinc-700"
                                                                >
                                                                    Edit
                                                                </button>
                                                            @endif
                                                            @if($reply->canBeDeletedBy(Auth::user()))
                                                                <button
                                                                    type="button"
                                                                    wire:click="deleteComment({{ $reply->id }})"
                                                                    wire:confirm="Are you sure you want to delete this reply?"
                                                                    class="text-xs text-red-500 hover:text-red-700"
                                                                >
                                                                    Delete
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
