<?php

namespace App\View\Components\Group;

use App\Concerns\GetsGroupData;
use App\Concerns\GetsLeaderboards;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class StatisticsTabs extends Component
{
    use GetsGroupData;

    public $group;

    public $anonymizePrivateUsers;

    public $leaderboards;

    public $memberOfGroup;

    public $initialTab;

    public function __construct(Group $group, $memberOfGroup, $anonymizePrivateUsers = false)
    {
        $this->group = $this->getGroupWithMemberships($group);
        $this->memberOfGroup = $memberOfGroup;
        $this->anonymizePrivateUsers = $anonymizePrivateUsers;
        $this->leaderboards = app(GetsLeaderboards::class)->getActive($group);
        $this->initialTab = $this->getSavedTab();
    }

    protected function getSavedTab(): int
    {
        $user = Auth::user();
        if (!$user) {
            return 2; // Default to "This Month" (tab index 2)
        }

        $membership = $this->group->memberships->firstWhere('user_id', $user->id);
        if (!$membership || !$membership->last_leaderboard_tab) {
            return 2;
        }

        // Map tab names to indices: 1=forever, 2=month, 3=week
        return match($membership->last_leaderboard_tab) {
            'forever' => 1,
            'month' => 2,
            'week' => 3,
            default => 2,
        };
    }

    public function render()
    {
        return view('components.group.statistics-tabs');
    }
}
