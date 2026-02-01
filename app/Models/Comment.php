<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('created_at');
    }

    public function allReplies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('allReplies');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeForScore($query, Score $score)
    {
        return $query->where('commentable_type', Score::class)
                     ->where('commentable_id', $score->id);
    }

    public function scopeDiscussionPosts($query)
    {
        return $query->whereNull('commentable_type')
                     ->whereNull('parent_id');
    }

    public function scopeForGroup($query, Group $group)
    {
        return $query->where('group_id', $group->id);
    }

    public function scopeForBoard($query, int $boardNumber, ?Group $group = null)
    {
        $query->where('commentable_type', 'board')
              ->where('commentable_id', $boardNumber);

        if ($group) {
            // Group-specific board comments
            $query->where('group_id', $group->id);
        } else {
            // Public board comments
            $query->whereNull('group_id');
        }

        return $query;
    }

    public function isBoardComment(): bool
    {
        return $this->commentable_type === 'board';
    }

    public function getBoardNumberAttribute(): ?int
    {
        if ($this->isBoardComment()) {
            return (int) $this->commentable_id;
        }
        return null;
    }

    public function isDiscussionPost()
    {
        return $this->commentable_type === null && $this->parent_id === null;
    }

    public function isReply()
    {
        return $this->parent_id !== null;
    }

    public function isScoreComment()
    {
        return $this->commentable_type === Score::class;
    }

    public function canBeEditedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->user_id === $user->id;
    }

    public function canBeDeletedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        // Author can delete
        if ($this->user_id === $user->id) {
            return true;
        }

        // Group admin can delete
        return $this->group->isAdmin($user);
    }

    public function canBeSeenBy(?User $user): bool
    {
        // If it's a score comment, follow score visibility rules
        if ($this->isScoreComment() && $this->commentable) {
            return $this->commentable->scoreCanBeSeenByUser($user);
        }

        // For discussion posts in public groups, anyone can see
        if ($this->group->public) {
            return true;
        }

        // For private groups, must be a member
        if (!$user) {
            return false;
        }

        return $this->group->isMemberOf($user);
    }
}
