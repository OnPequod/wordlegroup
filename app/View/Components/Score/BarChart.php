<?php

namespace App\View\Components\Score;

use Illuminate\View\Component;

class BarChart extends Component
{
    public $scores;

    public $labels = [1, 2, 3, 4, 5, 6, 'X'];

    public $values;

    public $stepSize;

    public $scoreDistribution;

    public function __construct($scoreDistribution, $stepSize = 1)
    {
        $distribution = collect($scoreDistribution ?? []);

        $this->values = collect($this->labels)
            ->map(function ($label) use ($distribution) {
                return (int) ($distribution->get($label, 0));
            })
            ->values();

        $this->stepSize = $this->getStepSize($this->values);
    }

    public function getStepSize($values): int
    {
        $maxCount = $values->max();

        if ($maxCount <= 10) {
            return 1;
        } elseif ($maxCount <= 100) {
            return 5;
        } else {
            return 10;
        }
    }

    public function getValues($scores)
    {
        return collect($this->labels)
            ->mapWithKeys(function ($score) use ($scores) {
                return [$score => $scores->where('score', $score === 'X' ? 7 : $score)->count()];
            })->values();
    }

    public function render()
    {
        return view('components.score.bar-chart');
    }
}
