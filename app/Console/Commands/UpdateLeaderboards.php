<?php

namespace App\Console\Commands;

use App\Concerns\UpdatesLeaderboards;
use App\Concerns\WordleDate;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateLeaderboards extends Command
{
    protected $signature = 'groups:update-leaderboards {--H|historical : Update historical leaderboards.}';

    protected $description = 'Update group leaderboards.';

    public $historical;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->historical = $this->option('historical');

        if ($this->historical) {
            $this->updateHistorical();

            return 0;
        }

        $this->updateCurrent();

        return 0;
    }

    public function updateCurrent()
    {
        Group::chunk(10, function ($groups) {
            foreach ($groups as $group) {
                $group->updateLeaderboards(now());
            }
        });

        $this->info('Group leaderboards updated.');
    }

    public function updateHistorical()
    {
        $earliestPossible = app(WordleDate::class)->getFirstBoardStartTime();

        $years = $this->getYears($earliestPossible);
        $months = $this->getMonths($years, $earliestPossible);
        $weeks = $this->getWeeks($years, $earliestPossible);

        Group::chunk(50, function ($groups) use ($years, $months, $weeks) {
            foreach ($groups as $group) {
                // Update Forever.
                app(UpdatesLeaderboards::class)->updateForever($group);

                // Update Years
                $years->each(fn($year) => app(UpdatesLeaderboards::class)->updateYear($group, $year));

                // Update Months
                $months->each(fn($month) => app(UpdatesLeaderboards::class)->updateMonth($group, $month));

                // Update Weeks
                $weeks->each(fn($week) => app(UpdatesLeaderboards::class)->updateWeek($group, $week));
            }
        });
    }

    public function getYears(Carbon $earliestPossible)
    {
        return collect(range($earliestPossible->year, now()->year, 1))
            ->map(function ($year) {
                return app(WordleDate::class)->get(Carbon::create()->setYear($year));
            });
    }

    public function getWeeks($years, Carbon $earliestPossible)
    {
        return $years
            ->map(function ($year) {
                return collect(range(1, 52, 1))
                    ->map(function ($week) use ($year) {
                        return app(WordleDate::class)
                            ->get($year->copy()->setISODate($year->year, $week));
                    });
            })
            ->flatten()
            ->filter(function ($week) use ($earliestPossible) {
                return $week > $earliestPossible && $week <= now();
            });
    }

    public function getMonths($years, Carbon $earliestPossible)
    {
        return $years
            ->map(function ($year) {
                return collect(range(1, 12, 1))
                    ->map(function ($month) use ($year) {
                        return $year->copy()->setMonth($month)->startOfMonth();
                    });
            })
            ->flatten()
            ->filter(function ($month) use ($earliestPossible) {
                return $month->endOfMonth() > $earliestPossible &&
                    $month->startOfMonth() <= now();
            });
    }
}
