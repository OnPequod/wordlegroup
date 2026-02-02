<?php

namespace App\Concerns;

use App\Models\Group;
use App\Models\Leaderboard;
use Carbon\Carbon;

class UpdatesLeaderboards
{
    public $leaderboards;

    public $date;

    public function __construct()
    {
        $this->leaderboards = config('settings.leaderboards');
        $this->date = app(WordleDate::class);
    }

    public function update(Group $group, ?Carbon $when = null)
    {
        if (!$when) {
            $when = app(WordleDate::class)->getActiveBoardEndTime();
        }

        if (in_array('forever', $this->leaderboards)) {
            $this->updateForever($group);
        }

        if (in_array('year', $this->leaderboards)) {
            $this->updateYear($group, $when->copy());
        }

        if (in_array('month', $this->leaderboards)) {
            $this->updateMonth($group, $when->copy());
        }

        if (in_array('week', $this->leaderboards)) {
            $this->updateWeek($group, $when->copy());
        }
    }

    public function updateForever(Group $group)
    {
        $startDate = $this->date->getFirstBoardStartTime();
        $endDate = $this->date->getActiveBoardEndTime();

        $this->saveLeaderboard($group, 'forever', $startDate, $endDate);
    }

    public function updateYear(Group $group, Carbon $when)
    {
        $startDate = max($this->date->get($when->copy()->startOfYear()), $this->date->getFirstBoardStartTime());
        $endDate = min($this->date->get($when->copy()->endOfYear()), $this->date->getLastPossibleBoardStartTime());

        $this->saveLeaderboard($group, 'year', $startDate, $endDate);
    }

    public function updateMonth(Group $group, Carbon $when)
    {
        $startDate = max($this->date->get($when->copy()->startOfMonth()), $this->date->getFirstBoardStartTime());
        $endDate = min($this->date->get($when->copy()->endOfMonth()), $this->date->getLastPossibleBoardStartTime());

        $this->saveLeaderboard($group, 'month', $startDate, $endDate);
    }

    public function updateWeek(Group $group, Carbon $when)
    {
        $startDate = max($this->date->get($when->copy()->startOfWeek()), $this->date->getFirstBoardStartTime());
        $endDate = min($this->date->get($when->copy()->endOfWeek()), $this->date->getLastPossibleBoardStartTime());

        $this->saveLeaderboard($group, 'week', $startDate, $endDate);
    }

    public function saveLeaderboard(Group $group, $for, $startDate, $endDate)
    {
        $summaryStats = $group->getSummaryStats($startDate, $endDate);


        // If no scores recorded, do not save leaderboard and exit;
        if ($summaryStats['scores_recorded'] === 0) {
            return;
        }

        $leaderboard = $group->getLeaderBoard($startDate, $endDate);
        $year = $this->getYear($for, $endDate);
        $month = $this->getMonth($for, $endDate);
        $week = $this->getWeek($for, $endDate);

        Leaderboard::updateOrCreate(
            ['group_id' => $group->id, 'for' => $for, 'year' => $year, 'month' => $month, 'week' => $week],
            [
                'member_count'       => $summaryStats['member_count'],
                'scores_recorded'    => $summaryStats['scores_recorded'],
                'score_mean'         => $summaryStats['score_mean'],
                'score_median'       => $summaryStats['score_median'],
                'score_mode'         => $summaryStats['score_mode'],
                'score_distribution' => $summaryStats['score_distribution'],
                'leaderboard'        => $leaderboard // foreverboard
            ]
        );
    }

    public function getYear($for, $date)
    {
        if ($for === 'forever') {
            return null;
        }

        return $date ? date('Y', $date->timestamp) : null;
    }

    public function getMonth($for, $date)
    {
        if (in_array($for, ['forever', 'year', 'week'])) {
            return null;
        }

        return $date ? date('m', $date->timestamp) : null;
    }

    public function getWeek($for, $date)
    {
        if (in_array($for, ['forever', 'year', 'month'])) {
            return null;
        }

        return $date ? $date->week : null;
    }
}
