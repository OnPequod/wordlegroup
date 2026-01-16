<?php

namespace App\Http\Livewire\Score;

use App\Models\Score;
use Livewire\Component;

class Share extends Component
{
    public $buttonClass;

    public $copyIcon;

    public $iconSize;

    public $groupCopyWithAllButtons;

    public $score;

    public $showCopyButton;

    public $confirm;

    public $showCopyIcon;

    public function mount(
        Score $score,
        $buttonClass = null,
        $groupCopyWithAllButtons = false,
        $showCopyIcon = false,
        $showCopyButton = true,
        $confirm = null,
        $iconSize = 4
    ) {
        $this->score = $score;
        $this->buttonClass = $buttonClass;
        $this->showCopyButton = $showCopyButton;
        $this->groupCopyWithAllButtons = $groupCopyWithAllButtons;
        $this->showCopyIcon = $showCopyIcon;
        $this->confirm = $confirm ?? !$score->user->public_profile && !$score->public;
        $this->iconSize = $iconSize;
    }

    public function shareScore($scoreId, $type)
    {
        if ($this->confirm) {
            $this->score->shared_at = now();
            $this->score->save();
        }

        $this->dispatch("shared-score-{$this->score->id}-to-{$type}");
    }

    protected function getListeners()
    {
        return ['shareScore'];
    }

    public function render()
    {
        return view('livewire.score.share');
    }
}
