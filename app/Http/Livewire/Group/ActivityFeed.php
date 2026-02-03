<?php

namespace App\Http\Livewire\Group;

use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityFeed extends Component
{
    use WithPagination;

    public $anonymizePrivateUsers;

    public $group;

    public $filterByUserId;

    public $user;

    public string $viewMode = 'list';

    public int $perPage = 6;

    public function mount(Group $group, $anonymizePrivateUsers = false, string $viewMode = 'list', ?int $filterByUserId = null)
    {
        $this->group = $group;
        $this->anonymizePrivateUsers = $anonymizePrivateUsers;
        $this->viewMode = $viewMode;
        $this->filterByUserId = $filterByUserId;
        $this->user = Auth::check() ? Auth::user() : null;
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedFilterByUserId(): void
    {
        $this->resetPage();
    }

    public function getScores()
    {
        $query = $this->group
            ->scores();

        if ($this->filterByUserId) {
            $query = $query->where('scores.user_id', $this->filterByUserId);
        }

        $query = $query->with('user')
                     ->latest('created_at')
                     ->latest('date');

        // Only load comments count in tiles view (expensive subquery)
        if ($this->viewMode === 'tiles') {
            $query = $query->withCount('comments');
        }

        return $query->paginate($this->perPage)->withPath(route('group.home', $this->group));

    }

    public function getIsGroupMemberProperty(): bool
    {
        if (!$this->user) {
            return false;
        }

        return $this->group->memberships()->where('user_id', $this->user->id)->exists();
    }

    public function render()
    {
        // Group members always see real names, regardless of anonymizePrivateUsers flag
        $shouldAnonymize = $this->anonymizePrivateUsers && !$this->isGroupMember;

        return view('livewire.group.activity-feed', [
            'scores' => $this->getScores(),
            'anonymizePrivateUsers' => $shouldAnonymize,
        ]);
    }
}
