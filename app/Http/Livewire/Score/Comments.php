<?php

namespace App\Http\Livewire\Score;

use App\Models\Comment;
use App\Models\Group;
use App\Models\Score;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Comments extends Component
{
    public Score $score;
    public ?Group $group = null;
    public $body = '';
    public $editingCommentId = null;
    public $editBody = '';
    public $replyingToCommentId = null;
    public $replyBody = '';

    protected $rules = [
        'body' => 'required|min:1|max:1000',
        'editBody' => 'required|min:1|max:1000',
        'replyBody' => 'required|min:1|max:1000',
    ];

    public function mount(Score $score, ?Group $group = null)
    {
        $this->score = $score;
        $this->group = $group;
    }

    public function getCommentsProperty()
    {
        return Comment::forScore($this->score)
            ->topLevel()
            ->with(['user', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function canComment(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // User must share a group with the score owner
        return $this->score->user->sharesGroupMembershipWithAnotherUser($user)
            || $this->score->user_id === $user->id;
    }

    public function addComment()
    {
        if (!$this->canComment()) {
            return;
        }

        $this->validateOnly('body');

        $user = Auth::user();

        // Find a shared group for this comment
        $groupId = $this->group?->id;
        if (!$groupId) {
            $sharedGroups = $user->getSharedGroupsWithAnotherUser($this->score->user);
            $groupId = $sharedGroups->first();
        }

        if (!$groupId) {
            return;
        }

        Comment::create([
            'user_id' => $user->id,
            'group_id' => $groupId,
            'commentable_type' => Score::class,
            'commentable_id' => $this->score->id,
            'body' => $this->body,
        ]);

        $this->body = '';
    }

    public function startEditing(Comment $comment)
    {
        if (!$comment->canBeEditedBy(Auth::user())) {
            return;
        }

        $this->editingCommentId = $comment->id;
        $this->editBody = $comment->body;
    }

    public function cancelEditing()
    {
        $this->editingCommentId = null;
        $this->editBody = '';
    }

    public function saveEdit()
    {
        $this->validateOnly('editBody');

        $comment = Comment::find($this->editingCommentId);

        if (!$comment || !$comment->canBeEditedBy(Auth::user())) {
            return;
        }

        $comment->update(['body' => $this->editBody]);

        $this->editingCommentId = null;
        $this->editBody = '';
    }

    public function startReplying(Comment $comment)
    {
        if (!$this->canComment()) {
            return;
        }

        $this->replyingToCommentId = $comment->id;
        $this->replyBody = '';
    }

    public function cancelReplying()
    {
        $this->replyingToCommentId = null;
        $this->replyBody = '';
    }

    public function saveReply()
    {
        if (!$this->canComment()) {
            return;
        }

        $this->validateOnly('replyBody');

        $parent = Comment::find($this->replyingToCommentId);

        if (!$parent) {
            return;
        }

        Comment::create([
            'user_id' => Auth::id(),
            'group_id' => $parent->group_id,
            'commentable_type' => $parent->commentable_type,
            'commentable_id' => $parent->commentable_id,
            'parent_id' => $parent->id,
            'body' => $this->replyBody,
        ]);

        $this->replyingToCommentId = null;
        $this->replyBody = '';
    }

    public function deleteComment(Comment $comment)
    {
        if (!$comment->canBeDeletedBy(Auth::user())) {
            return;
        }

        $comment->delete();
    }

    public function render()
    {
        return view('livewire.score.comments');
    }
}
