<?php

namespace App\Http\Livewire;

use App\Concerns\WordleDate;
use App\Models\DailySummary;
use App\Models\Puzzle;
use App\Models\PublicLeaderboard as PublicLeaderboardModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PublicLeaderboard extends Component
{
    public string $period = 'forever';

    // User settings for inline form
    public ?string $publicAlias = null;
    public bool $showOnPublicLeaderboard = false;
    public bool $showNameOnPublicLeaderboard = false;

    public function mount(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->publicAlias = $user->public_alias;
            $this->showOnPublicLeaderboard = (bool) $user->show_on_public_leaderboard;
            $this->showNameOnPublicLeaderboard = (bool) $user->show_name_on_public_leaderboard;
        }
    }

    public function saveSettings(): void
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        $user->public_alias = $this->publicAlias;
        $user->show_on_public_leaderboard = $this->showOnPublicLeaderboard;
        $user->show_name_on_public_leaderboard = $this->showNameOnPublicLeaderboard;
        $user->save();

        session()->flash('message', 'Settings saved.');
    }

    public function getLeaderboardProperty()
    {
        $now = now();

        return match ($this->period) {
            'month' => PublicLeaderboardModel::getMonth($now->year, $now->month),
            'week' => PublicLeaderboardModel::getWeek($now->year, $now->week),
            default => PublicLeaderboardModel::getForever(),
        };
    }

    public function getPeriodLabelProperty()
    {
        return match ($this->period) {
            'month' => 'This Month',
            'week' => 'This Week',
            default => 'All Time',
        };
    }

    public function getTodaysPuzzleProperty(): ?Puzzle
    {
        return Puzzle::getToday();
    }

    public function getTodaysSummaryProperty(): ?DailySummary
    {
        $boardNumber = app(WordleDate::class)->activeBoardNumber;

        return DailySummary::getByBoardNumber($boardNumber);
    }

    public function getRecentPuzzlesProperty()
    {
        $currentBoard = app(WordleDate::class)->activeBoardNumber;

        return Puzzle::where('board_number', '<', $currentBoard)
            ->orderByDesc('board_number')
            ->limit(5)
            ->get();
    }

    public function getRecordedBoardsProperty(): array
    {
        if (!Auth::check()) {
            return [];
        }

        return Auth::user()->scores()->pluck('board_number')->toArray();
    }

    public function canViewAnswer(int $boardNumber): bool
    {
        return in_array($boardNumber, $this->recordedBoards);
    }

    public function setPeriod($period)
    {
        $this->period = $period;
    }

    public function render()
    {
        return view('livewire.public-leaderboard')
            ->layout('layouts.app');
    }
}
