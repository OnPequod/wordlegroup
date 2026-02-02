<?php

namespace App\Http\Livewire;

use App\Concerns\WordleDate;
use App\Models\DailySummary;
use App\Models\Puzzle;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class BoardArchive extends Component
{
    use WithPagination;

    public ?int $year = null;
    public ?int $month = null;

    public function mount(?int $year = null, ?int $month = null): void
    {
        $this->year = $year;
        $this->month = $month;

        // Validate year/month if provided
        if ($this->year && ($this->year < 2021 || $this->year > now()->year)) {
            abort(404);
        }
        if ($this->month && ($this->month < 1 || $this->month > 12)) {
            abort(404);
        }
    }

    public function getAvailableYearsProperty(): array
    {
        return range(now()->year, 2021);
    }

    public function getAvailableMonthsProperty(): array
    {
        return collect(range(1, 12))->mapWithKeys(function ($month) {
            return [$month => date('F', mktime(0, 0, 0, $month, 1))];
        })->all();
    }

    public function getPuzzlesProperty()
    {
        $query = Puzzle::query()
            ->where('board_number', '<=', app(WordleDate::class)->activeBoardNumber)
            ->orderByDesc('board_number');

        if ($this->year) {
            $query->whereYear('puzzle_date', $this->year);
        }
        if ($this->month) {
            $query->whereMonth('puzzle_date', $this->month);
        }

        return $query->paginate(50);
    }

    public function getTotalPuzzlesProperty(): int
    {
        return Puzzle::where('board_number', '<=', app(WordleDate::class)->activeBoardNumber)->count();
    }

    public function getPageTitleProperty(): string
    {
        if ($this->year && $this->month) {
            return date('F', mktime(0, 0, 0, $this->month, 1)) . ' ' . $this->year;
        }
        if ($this->year) {
            return (string) $this->year;
        }
        return 'All Time';
    }

    public function filterByYear(?int $year): void
    {
        $this->year = $year;
        $this->month = null;
        $this->resetPage();
    }

    public function filterByMonth(?int $month): void
    {
        $this->month = $month;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->year = null;
        $this->month = null;
        $this->resetPage();
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

    public function getSummariesProperty()
    {
        $boardNumbers = $this->puzzles->pluck('board_number');

        return DailySummary::whereIn('board_number', $boardNumbers)
            ->get()
            ->keyBy('board_number');
    }

    public function render()
    {
        return view('livewire.board-archive')
            ->layout('layouts.app');
    }
}
