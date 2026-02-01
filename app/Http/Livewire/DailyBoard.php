<?php

namespace App\Http\Livewire;

use App\Concerns\WordleDate;
use App\Models\Comment;
use App\Models\DailySummary;
use App\Models\Puzzle;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DailyBoard extends Component
{
    public ?int $boardNumber = null;
    public string $newComment = '';

    public function mount(?int $boardNumber = null): void
    {
        $wordleDate = app(WordleDate::class);

        $this->boardNumber = $boardNumber ?? $wordleDate->activeBoardNumber;

        // Validate board number is within valid range
        if ($this->boardNumber < 0 || $this->boardNumber > $wordleDate->activeBoardNumber) {
            abort(404);
        }
    }

    public function getSummaryProperty(): ?DailySummary
    {
        return DailySummary::getByBoardNumber($this->boardNumber);
    }

    public function getPuzzleProperty(): ?Puzzle
    {
        return Puzzle::getByBoardNumber($this->boardNumber);
    }

    public function getCanViewAnswerProperty(): bool
    {
        // Can only view answer if logged in and has recorded this board
        return $this->userHasRecorded;
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

        return Auth::user()->recordedBoard($this->boardNumber);
    }

    public function getCanViewBoardsProperty(): bool
    {
        // Can view boards if:
        // 1. Not the current puzzle, OR
        // 2. User has recorded their score for this board
        if (!$this->isCurrentPuzzle) {
            return true;
        }

        return $this->userHasRecorded;
    }

    public function goToPrevious(): void
    {
        if ($this->hasPrevious) {
            $this->redirect(route('board', $this->boardNumber - 1));
        }
    }

    public function goToNext(): void
    {
        if ($this->hasNext) {
            $this->redirect(route('board', $this->boardNumber + 1));
        }
    }

    public function goToToday(): void
    {
        $this->redirect(route('board'));
    }

    public function getCommentsProperty()
    {
        return Comment::forBoard($this->boardNumber)
            ->topLevel()
            ->with(['user', 'replies.user'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function addComment(): void
    {
        if (!Auth::check()) {
            return;
        }

        $this->validate([
            'newComment' => 'required|string|max:1000',
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'commentable_type' => 'board',
            'commentable_id' => $this->boardNumber,
            'body' => $this->newComment,
        ]);

        $this->newComment = '';
    }

    public function deleteComment(int $commentId): void
    {
        $comment = Comment::find($commentId);

        if (!$comment || !$comment->canBeDeletedBy(Auth::user())) {
            return;
        }

        $comment->delete();
    }

    public function render()
    {
        return view('livewire.daily-board')
            ->layout('layouts.app');
    }
}
