<?php

namespace App\Http\Livewire\Account;

use App\Models\User;
use App\Services\AuthenticatedUserService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ScoreFeed extends Component
{
    use WithPagination;

    public $user;

    public $showWhenRecordedByOtherUser;

    public $ownedByViewingUser;

    public $viewingUser;

    public function mount(User $user, $showWhenRecordedByOtherUser = false)
    {
        $this->user = $user;
        $this->viewingUser = app(AuthenticatedUserService::class)->get();
        $this->ownedByViewingUser = $this->viewingUser && $this->viewingUser->id === $user->id;
        $this->showWhenRecordedByOtherUser = $showWhenRecordedByOtherUser;
    }

    public function render()
    {
        $scores = $this->user
            ->dailyScores()
            ->latest('date')
            ->paginate(6)
            ->onEachSide(1);

        // Set the user relation directly to avoid an extra query
        // since all scores belong to $this->user
        foreach ($scores as $score) {
            $score->setRelation('user', $this->user);
        }

        return view('livewire.account.score-feed', [
            'scores' => $scores
        ]);
    }
}
