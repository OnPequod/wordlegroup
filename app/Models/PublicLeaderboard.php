<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicLeaderboard extends Model
{
    protected $guarded = [];

    protected $casts = [
        'leaderboard' => 'collection',
    ];

    public function scopeFor($query, string $for)
    {
        return $query->where('for', $for);
    }

    public function scopeForever($query)
    {
        return $query->where('for', 'forever');
    }

    public function scopeYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function scopeMonth($query, int $month)
    {
        return $query->where('month', $month);
    }

    public function scopeWeek($query, int $week)
    {
        return $query->where('week', $week);
    }

    public function scopeForDate($query, \Carbon\Carbon $date)
    {
        return $query->where(function ($q) use ($date) {
            $q->where('for', 'forever')
              ->orWhere(function ($q) use ($date) {
                  $q->where('for', 'month')
                    ->where('year', $date->year)
                    ->where('month', $date->month);
              })
              ->orWhere(function ($q) use ($date) {
                  $q->where('for', 'week')
                    ->where('year', $date->year)
                    ->where('week', $date->week);
              });
        });
    }

    public static function getForever()
    {
        return static::for('forever')->first();
    }

    public static function getMonth(int $year, int $month)
    {
        return static::for('month')
            ->year($year)
            ->month($month)
            ->first();
    }

    public static function getWeek(int $year, int $week)
    {
        return static::for('week')
            ->year($year)
            ->week($week)
            ->first();
    }
}
