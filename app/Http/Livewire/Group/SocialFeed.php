<?php

namespace App\Http\Livewire\Group;

use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class SocialFeed extends Component
{
    public Group $group;

    public string $activeTab = 'activity';

    public string $viewMode = 'list';

    public ?int $filterByUserId = null;

    public bool $anonymizePrivateUsers = false;

    public function mount(Group $group, bool $anonymizePrivateUsers = false): void
    {
        $this->group = $group;
        $this->anonymizePrivateUsers = $anonymizePrivateUsers;

        $user = Auth::user();
        if (!$user) {
            return;
        }

        // Load user's saved tab preference for this group
        $membership = $this->getMembership();
        if ($membership && $membership->last_active_tab) {
            $this->activeTab = $membership->last_active_tab;
        } elseif ($this->unreadDiscussionCount > 0) {
            // Default to Discussion tab if there are unread messages
            $this->activeTab = 'discussion';
        }

        // Mark current tab as viewed
        $this->markTabAsViewed();
    }

    protected function getMembership()
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        return $this->group->memberships()->where('user_id', $user->id)->first();
    }

    protected function saveTabPreference(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $this->group->memberships()
            ->where('user_id', $user->id)
            ->update(['last_active_tab' => $this->activeTab]);
    }

    protected function markTabAsViewed(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $column = $this->activeTab === 'discussion'
            ? 'last_viewed_discussions_at'
            : 'last_viewed_activity_at';

        $this->group->memberships()
            ->where('user_id', $user->id)
            ->update([$column => now()]);
    }

    public function getUnreadDiscussionCountProperty(): int
    {
        return $this->group->getUnreadDiscussionCountFor(Auth::user());
    }

    public function getNewActivityCountProperty(): int
    {
        $user = Auth::user();
        if (!$user) {
            return 0;
        }

        $membership = $this->group->memberships()->where('user_id', $user->id)->first();
        if (!$membership || !$membership->last_viewed_activity_at) {
            return 0;
        }

        return $this->group->scores()
            ->where('created_at', '>', $membership->last_viewed_activity_at)
            ->count();
    }

    public function getIsGroupMemberProperty(): bool
    {
        return $this->group->isMemberOf(Auth::user());
    }

    public function getHasScoresProperty(): bool
    {
        return $this->group->scores_recorded > 0;
    }

    public function switchToActivity(): void
    {
        $this->activeTab = 'activity';
        $this->saveTabPreference();
        $this->markTabAsViewed();
    }

    public function switchToDiscussion(): void
    {
        $this->activeTab = 'discussion';
        $this->saveTabPreference();
        $this->markTabAsViewed();
    }

    public function clearUserFilter(): void
    {
        $this->filterByUserId = null;
    }

    #[On('discussionPosted')]
    public function onDiscussionPosted(): void
    {
        $this->switchToDiscussion();
    }

    #[On('replyToScore')]
    public function onReplyToScore(): void
    {
        $this->switchToDiscussion();
    }

    public function render()
    {
        return view('livewire.group.social-feed');
    }
}
