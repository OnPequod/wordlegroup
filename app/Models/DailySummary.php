<?php

namespace App\Models;

use App\Concerns\WordleDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySummary extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'puzzle_date' => 'date',
        'score_distribution' => 'collection',
        'wg_score_distribution' => 'collection',
        'boards' => 'collection',
    ];

    public function scopeBoardNumber($query, int $boardNumber)
    {
        return $query->where('board_number', $boardNumber);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('puzzle_date', $date);
    }

    public static function getByBoardNumber(int $boardNumber): ?self
    {
        return static::boardNumber($boardNumber)->first();
    }

    public function getIsCurrentPuzzleAttribute(): bool
    {
        return $this->board_number === app(WordleDate::class)->activeBoardNumber;
    }

    public function getDifficultyLabelAttribute(): ?string
    {
        if ($this->difficulty_delta === null) {
            return null;
        }

        return match (true) {
            $this->difficulty_delta <= -0.5 => 'Easy',
            $this->difficulty_delta >= 0.5 => 'Hard',
            default => 'Average',
        };
    }

    public function getDifficultyColorAttribute(): string
    {
        return match ($this->difficultyLabel) {
            'Easy' => 'text-green-600',
            'Hard' => 'text-red-600',
            default => 'text-zinc-600',
        };
    }

    public function getFormattedDistributionAttribute(): array
    {
        // Use WG distribution if public distribution is empty
        $distribution = $this->score_distribution;
        if (!$distribution || $distribution->sum() === 0) {
            $distribution = $this->wg_score_distribution;
        }

        if (!$distribution) {
            return [];
        }

        $total = $distribution->sum();

        return collect([1, 2, 3, 4, 5, 6, 'X'])
            ->mapWithKeys(function ($score) use ($distribution, $total) {
                $count = $distribution[$score] ?? 0;
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

    public function getFormattedWgDistributionAttribute(): array
    {
        if (!$this->wg_score_distribution) {
            return [];
        }

        $distribution = $this->wg_score_distribution;
        $total = $distribution->sum();

        return collect([1, 2, 3, 4, 5, 6, 'X'])
            ->mapWithKeys(function ($score) use ($distribution, $total) {
                $count = $distribution[$score] ?? 0;
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
}
