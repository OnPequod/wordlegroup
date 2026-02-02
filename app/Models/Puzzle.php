<?php

namespace App\Models;

use App\Concerns\WordleDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puzzle extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'puzzle_date' => 'date',
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

    public static function getToday(): ?self
    {
        $boardNumber = app(WordleDate::class)->activeBoardNumber;

        return static::getByBoardNumber($boardNumber);
    }

    public function getIsCurrentPuzzleAttribute(): bool
    {
        return $this->board_number === app(WordleDate::class)->activeBoardNumber;
    }
}
