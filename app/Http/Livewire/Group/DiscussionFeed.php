<?php

namespace App\Http\Livewire\Group;

use App\Models\Comment;
use App\Models\Group;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DiscussionFeed extends Component
{
    public Group $group;

    public string $viewMode = 'list';

    public ?int $filterByUserId = null;

    public string $body = '';

    public ?int $editingPostId = null;

    public string $editBody = '';

    public ?int $replyingToPostId = null;

    public string $replyBody = '';

    public int $displayLimit = 15;

    public bool $showingEarlier = false;

    protected $rules = [
        'body' => 'required|min:1|max:2000',
        'editBody' => 'required|min:1|max:2000',
        'replyBody' => 'required|min:1|max:2000',
    ];

    public function mount(Group $group, string $viewMode = 'list', ?int $filterByUserId = null): void
    {
        $this->group = $group;
        $this->viewMode = $viewMode;
        $this->filterByUserId = $filterByUserId;

        // Mark discussions as viewed for members
        $this->markAsViewed();
    }

    protected function markAsViewed(): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        // Direct update to avoid loading model and triggering observer
        $this->group->memberships()
            ->where('user_id', $user->id)
            ->update(['last_viewed_discussions_at' => now()]);
    }

    public function getPostsProperty(): Collection
    {
        $limit = $this->showingEarlier ? $this->displayLimit * 3 : $this->displayLimit;

        return $this->group->discussionPosts()
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getGroupedPostsProperty(): Collection
    {
        $posts = $this->posts;

        // Group posts by day (newest day first) and collapse consecutive messages from same user
        return $posts->groupBy(function ($post) {
            return $post->created_at->format('Y-m-d');
        })->sortKeysDesc()->map(function ($dayPosts, $date) {
            $grouped = collect();
            $currentGroup = null;

            foreach ($dayPosts as $post) {
                if ($currentGroup === null || $currentGroup['user_id'] !== $post->user_id) {
                    if ($currentGroup !== null) {
                        $grouped->push($currentGroup);
                    }
                    $currentGroup = [
                        'user_id' => $post->user_id,
                        'user' => $post->user,
                        'posts' => collect([$post]),
                        'first_post_at' => $post->created_at,
                    ];
                } else {
                    $currentGroup['posts']->push($post);
                }
            }

            if ($currentGroup !== null) {
                $grouped->push($currentGroup);
            }

            return [
                'date' => $date,
                'groups' => $grouped,
            ];
        });
    }

    public function getHasEarlierMessagesProperty(): bool
    {
        $limit = $this->showingEarlier ? $this->displayLimit * 3 : $this->displayLimit;

        return $this->group->discussionPosts()->count() > $limit;
    }

    public function showEarlierMessages(): void
    {
        $this->showingEarlier = true;
    }

    public function canPost(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $this->group->isMemberOf($user);
    }

    public function createPost(): void
    {
        if (!$this->canPost()) {
            return;
        }

        $this->validate([
            'body' => 'required|min:1|max:2000',
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'group_id' => $this->group->id,
            'body' => $this->body,
        ]);

        $this->body = '';
        $this->markAsViewed();
        $this->dispatch('discussionPosted');
    }

    public function startEditing(Comment $post): void
    {
        if (!$post->canBeEditedBy(Auth::user())) {
            return;
        }

        $this->editingPostId = $post->id;
        $this->editBody = $post->body;
    }

    public function cancelEditing(): void
    {
        $this->editingPostId = null;
        $this->editBody = '';
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editBody' => 'required|min:1|max:2000',
        ]);

        $post = Comment::find($this->editingPostId);

        if (!$post || !$post->canBeEditedBy(Auth::user())) {
            return;
        }

        $post->update([
            'body' => $this->editBody,
        ]);

        $this->editingPostId = null;
        $this->editBody = '';
    }

    public function startReplying(Comment $post): void
    {
        if (!$this->canPost()) {
            return;
        }

        $this->replyingToPostId = $post->id;
        $this->replyBody = '';
    }

    public function cancelReplying(): void
    {
        $this->replyingToPostId = null;
        $this->replyBody = '';
    }

    public function saveReply(): void
    {
        if (!$this->canPost()) {
            return;
        }

        $this->validateOnly('replyBody');

        $parent = Comment::find($this->replyingToPostId);

        if (!$parent) {
            return;
        }

        Comment::create([
            'user_id' => Auth::id(),
            'group_id' => $this->group->id,
            'parent_id' => $parent->id,
            'body' => $this->replyBody,
        ]);

        $this->replyingToPostId = null;
        $this->replyBody = '';
        $this->markAsViewed();
        $this->dispatch('discussionPosted');
    }

    public function deletePost(Comment $post): void
    {
        if (!$post->canBeDeletedBy(Auth::user())) {
            return;
        }

        $post->delete();
    }

    public function render()
    {
        return view('livewire.group.discussion-feed');
    }
}
