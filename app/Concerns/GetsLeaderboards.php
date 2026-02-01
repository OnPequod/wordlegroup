<?php

namespace App\Concerns;

use App\Models\Group;
use App\Models\Leaderboard;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GetsLeaderboards
{
    public $date;

    public $leaderboards;

    public function __construct()
    {
        $this->leaderboards = config('settings.leaderboards');
        $this->date = app(WordleDate::class);
    }

    public function getActive(Group $group)
    {
        $when = app(WordleDate::class)->get(now());

        return $this->get($group, $when);
    }

    public function get(Group $group, Carbon $when = null)
    {
        if (!$when) {
            $when = app(WordleDate::class)->get(now());
        }

        $leaderboards = $group->leaderboards()->forDate($when)->get();

        return $this->mapsUsersToLeaderboards($group, $leaderboards);
    }

    public function mapsUsersToLeaderboards($group, $leaderboards, User $viewingUser = null)
    {
        $group->loadMissing('memberships.user');

        return collect($leaderboards)
            ->map(function ($leaderboard) use ($group, $viewingUser) {
                if ($leaderboard) {
                    $leaderboard->leaderboard = $leaderboard->leaderboard
                        ->map(function ($position) use ($group, $viewingUser) {
                            $position['user'] = $group->memberships->firstWhere('user_id', $position['user_id'])->user;

                            $position['can_be_seen_by_viewing_user'] = $viewingUser
                                ? $position['user']->profileCanBeSeenBy($viewingUser)
                                : true;

                            $position['user']['public_name'] = $position['can_be_seen_by_viewing_user'] ? $position['user']->name : 'Anonymous User';

                            // Add bot stats from user
                            $position['stats']['bot_skill_mean'] = $position['user']->bot_skill_mean;
                            $position['stats']['bot_luck_mean'] = $position['user']->bot_luck_mean;

                            return $position;
                        });

                }

                return $leaderboard;
            });
    }

    public function getForever(Group $group)
    {
        return Leaderboard::group($group->id)
                          ->for('forever')
                          ->first();
    }

    public function getYear(Group $group, Carbon $when)
    {
        return Leaderboard::group($group->id)
                          ->for('year')
                          ->year($when->year)
                          ->first();
    }

    public function getMonth(Group $group, Carbon $when)
    {
        return Leaderboard::group($group->id)
                          ->for('month')
                          ->year($when->year)
                          ->month($when->month)
                          ->first();
    }

    public function getWeek(Group $group, Carbon $when)
    {
        return Leaderboard::group($group->id)
                          ->for('week')
                          ->year($when->year)
                          ->month($when->month)
                          ->week($when->week)
                          ->first();
    }
}
