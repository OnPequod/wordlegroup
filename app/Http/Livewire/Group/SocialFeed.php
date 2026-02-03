<?php

namespace App\Http\Livewire\Group;

use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SocialFeed extends Component
{
    public Group $group;

    public string $viewMode = 'list';

    public ?int $filterByUserId = null;

    public bool $anonymizePrivateUsers = false;

    public function mount(Group $group, bool $anonymizePrivateUsers = false): void
    {
        $this->group = $group;
        $this->anonymizePrivateUsers = $anonymizePrivateUsers;

        // Mark both sections as viewed since they're both visible now
        $this->markAsViewed();
    }

    protected function markAsViewed(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $this->group->memberships()
            ->where('user_id', $user->id)
            ->update([
                'last_viewed_activity_at' => now(),
                'last_viewed_discussions_at' => now(),
            ]);
    }

    public function getUnreadDiscussionCountProperty(): int
    {
        return $this->group->getUnreadDiscussionCountFor(Auth::user());
    }

    public function getIsGroupMemberProperty(): bool
    {
        return $this->group->isMemberOf(Auth::user());
    }

    public function getHasScoresProperty(): bool
    {
        return $this->group->scores_recorded > 0;
    }

    public function clearUserFilter(): void
    {
        $this->filterByUserId = null;
    }

    public function render()
    {
        return view('livewire.group.social-feed');
    }
}
