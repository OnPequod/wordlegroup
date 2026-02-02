<?php

namespace App\Concerns;

use App\Models\PublicLeaderboard;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UpdatesPublicLeaderboards
{
    public function update(?Carbon $when = null): void
    {
        if (!$when) {
            $when = now();
        }

        $this->updateForever();
        $this->updateMonth($when);
        $this->updateWeek($when);
    }

    public function updateForever(): void
    {
        $leaderboard = $this->calculateLeaderboard();

        PublicLeaderboard::updateOrCreate(
            ['for' => 'forever', 'year' => null, 'month' => null, 'week' => null],
            [
                'participant_count' => $leaderboard->count(),
                'score_mean' => $leaderboard->avg('stats.mean'),
                'leaderboard' => $leaderboard->take(100)->values(),
            ]
        );
    }

    public function updateMonth(Carbon $when): void
    {
        $startBoard = app(WordleBoard::class)->getBoardNumberFromDate($when->copy()->startOfMonth());
        $endBoard = app(WordleBoard::class)->getBoardNumberFromDate($when);

        $leaderboard = $this->calculateLeaderboard($startBoard, $endBoard);

        PublicLeaderboard::updateOrCreate(
            ['for' => 'month', 'year' => $when->year, 'month' => $when->month, 'week' => null],
            [
                'participant_count' => $leaderboard->count(),
                'score_mean' => $leaderboard->avg('stats.mean'),
                'leaderboard' => $leaderboard->take(100)->values(),
            ]
        );
    }

    public function updateWeek(Carbon $when): void
    {
        $startBoard = app(WordleBoard::class)->getBoardNumberFromDate($when->copy()->startOfWeek());
        $endBoard = app(WordleBoard::class)->getBoardNumberFromDate($when);

        $leaderboard = $this->calculateLeaderboard($startBoard, $endBoard);

        PublicLeaderboard::updateOrCreate(
            ['for' => 'week', 'year' => $when->year, 'month' => null, 'week' => $when->week],
            [
                'participant_count' => $leaderboard->count(),
                'score_mean' => $leaderboard->avg('stats.mean'),
                'leaderboard' => $leaderboard->take(100)->values(),
            ]
        );
    }

    protected function calculateLeaderboard(?int $startBoard = null, ?int $endBoard = null)
    {
        // Get opted-in users who have been registered for at least 1 week
        $users = User::where('show_on_public_leaderboard', true)
            ->where('created_at', '<=', now()->subWeek())
            ->with(['dailyScores' => function ($query) use ($startBoard, $endBoard) {
                if ($startBoard !== null && $endBoard !== null) {
                    $query->whereBetween('scores.board_number', [$startBoard, $endBoard]);
                }
            }])
            ->get();

        $userScores = $users
            ->map(function ($user) {
                $scores = $user->dailyScores;

                if ($scores->isEmpty()) {
                    return null;
                }

                $botSkillScores = $scores->whereNotNull('bot_skill_score');
                $botLuckScores = $scores->whereNotNull('bot_luck_score');

                return [
                    'user_id' => $user->id,
                    'name' => $user->public_display_name,
                    'show_name' => $user->show_name_on_public_leaderboard,
                    'stats' => [
                        'mean' => round($scores->average('score'), 2),
                        'count' => $scores->count(),
                        'bot_skill_mean' => $botSkillScores->isNotEmpty()
                            ? round($botSkillScores->average('bot_skill_score'), 1)
                            : null,
                        'bot_luck_mean' => $botLuckScores->isNotEmpty()
                            ? round($botLuckScores->average('bot_luck_score'), 1)
                            : null,
                    ],
                ];
            })
            ->filter()
            ->sortBy('stats.mean');

        // Calculate places
        $placeNumbers = $userScores->pluck('stats.mean')
            ->unique()
            ->sort()
            ->values()
            ->map(function ($score, $index) {
                return [
                    'place' => $index + 1,
                    'score' => $score,
                ];
            });

        return $userScores
            ->map(function ($userScore) use ($placeNumbers) {
                $place = $placeNumbers->firstWhere('score', $userScore['stats']['mean'])['place'];
                $userScore['place'] = $place;
                return $userScore;
            })
            ->sortBy('place')
            ->values();
    }
}
