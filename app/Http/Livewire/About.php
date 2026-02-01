<?php

namespace App\Http\Livewire;

use App\Models\Group;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class About extends Component
{
    public function getStatsProperty(): array
    {
        return [
            'users' => User::count(),
            'groups' => Group::count(),
            'scores' => Score::count(),
        ];
    }

    public function getScoreDistributionProperty(): array
    {
        $distribution = Score::query()
            ->select('score', DB::raw('COUNT(*) as count'))
            ->groupBy('score')
            ->pluck('count', 'score')
            ->all();

        $total = array_sum($distribution);

        return collect([1, 2, 3, 4, 5, 6, 'X'])
            ->mapWithKeys(function ($score) use ($distribution, $total) {
                $key = $score === 'X' ? 7 : $score;
                $count = $distribution[$key] ?? 0;
                $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;

                return [
                    $score => [
                        'count' => $count,
                        'percentage' => $percentage,
                    ],
                ];
            })
            ->all();
    }

    public function render()
    {
        return view('livewire.about');
    }
}
