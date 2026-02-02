<?php

namespace App\Http\Livewire\Score;

use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SharePage extends Component
{
    public $score;

    public $title;

    public $user;

    public $viewingUser;

    public $boardVisible;

    public $beingViewedByOwner;

    public function mount(Score $score)
    {
        $score->load('user');
        $this->score = $score;
        $this->user = $score->user;
        $this->viewingUser = Auth::check() ? Auth::user() : null;
        $this->beingViewedByOwner = $this->getBeingViewedByOwner($this->user, $this->viewingUser);

        if ($score->scoreCannotBeSeenByUser($this->viewingUser)) {
            abort(403);
        }

        $this->boardVisible = $score->boardCanBeSeenByUser($this->viewingUser);

        $this->title = $this->getTitle($score);
    }

    public function getBeingViewedByOwner(User $user, ?User $viewingUser = null)
    {

        if(! $viewingUser) {
            return false;
        }

        return $user->id === $viewingUser->id;
    }

    public function getTitle(Score $score)
    {
        return "Wordle {$score->board_number}";
    }

    public function render()
    {
        return view('livewire.score.share-page');
    }
}
