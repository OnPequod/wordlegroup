<?php

namespace App\Http\Livewire;

use App\Concerns\WordleBoard;
use App\Concerns\WordleDate;
use App\Models\DailySummary;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DailyPuzzle extends Component
{
    public int $boardNumber;

    public function mount(?int $boardNumber = null): void
    {
        $wordleDate = app(WordleDate::class);

        $this->boardNumber = $boardNumber ?? $wordleDate->activeBoardNumber;

        // Validate board number is within range
        if ($this->boardNumber < 0 || $this->boardNumber > $wordleDate->activeBoardNumber) {
            $this->boardNumber = $wordleDate->activeBoardNumber;
        }
    }

    public function getSummaryProperty(): ?DailySummary
    {
        return DailySummary::getByBoardNumber($this->boardNumber);
    }

    public function getPuzzleDateProperty()
    {
        return app(WordleDate::class)->getDateFromBoardNumber($this->boardNumber);
    }

    public function getIsCurrentPuzzleProperty(): bool
    {
        return $this->boardNumber === app(WordleDate::class)->activeBoardNumber;
    }

    public function getHasPreviousProperty(): bool
    {
        return $this->boardNumber > 0;
    }

    public function getHasNextProperty(): bool
    {
        return $this->boardNumber < app(WordleDate::class)->activeBoardNumber;
    }

    public function getUserHasRecordedProperty(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->scores()->where('board_number', $this->boardNumber)->exists();
    }

    public function getCanViewBoardsProperty(): bool
    {
        // Can view boards if:
        // 1. It's not today's puzzle, OR
        // 2. User has recorded their score for this puzzle
        return !$this->isCurrentPuzzle || $this->userHasRecorded;
    }

    public function goToPrevious(): void
    {
        if ($this->hasPrevious) {
            $this->boardNumber--;
        }
    }

    public function goToNext(): void
    {
        if ($this->hasNext) {
            $this->boardNumber++;
        }
    }

    public function goToToday(): void
    {
        $this->boardNumber = app(WordleDate::class)->activeBoardNumber;
    }

    public function goToBoard(int $boardNumber): void
    {
        $wordleDate = app(WordleDate::class);

        if ($boardNumber >= 0 && $boardNumber <= $wordleDate->activeBoardNumber) {
            $this->boardNumber = $boardNumber;
        }
    }

    public function render()
    {
        return view('livewire.daily-puzzle')
            ->layout('layouts.app');
    }
}
