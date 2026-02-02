<?php

namespace App\Concerns;

use App\Models\DailySummary;
use App\Models\PublicLeaderboard;
use App\Models\Score;
use App\Models\User;

class UpdatesDailySummaries
{
    public function updateRecent(): void
    {
        $wordleDate = app(WordleDate::class);
        $todayBoardNumber = $wordleDate->activeBoardNumber;
        $yesterdayBoardNumber = $todayBoardNumber - 1;

        $this->updateForBoardNumber($todayBoardNumber);
        $this->updateForBoardNumber($yesterdayBoardNumber);
    }

    public function rebuildAll(): void
    {
        $wordleDate = app(WordleDate::class);
        $currentBoardNumber = $wordleDate->activeBoardNumber;

        $boardNumbers = Score::query()
            ->distinct()
            ->pluck('board_number')
            ->sort()
            ->values();

        foreach ($boardNumbers as $boardNumber) {
            $this->updateForBoardNumber($boardNumber);
        }
    }

    public function updateForBoardNumber(int $boardNumber): void
    {
        $publicScores = $this->getPublicScoresForBoard($boardNumber);
        $allScores = $this->getAllScoresForBoard($boardNumber);

        if ($allScores->isEmpty()) {
            DailySummary::where('board_number', $boardNumber)->delete();
            return;
        }

        $puzzleDate = app(WordleDate::class)->getDateFromBoardNumber($boardNumber);
        $allTimeMean = $this->getAllTimeMean();

        // Public leaderboard stats (opted-in users only)
        $scoreMean = $publicScores->isNotEmpty() ? round($publicScores->avg('score'), 2) : null;
        $scoreMedian = $this->calculateMedian($publicScores->pluck('score'));
        $distribution = $this->calculateDistribution($publicScores);
        $botSkillMean = $this->calculateBotSkillMean($publicScores);
        $botLuckMean = $this->calculateBotLuckMean($publicScores);
        $difficultyDelta = $allTimeMean !== null && $scoreMean !== null ? round($scoreMean - $allTimeMean, 2) : null;
        $boards = $this->buildBoardsData($publicScores);

        // WordleGroup stats (all users)
        $wgScoreMean = round($allScores->avg('score'), 2);
        $wgDistribution = $this->calculateDistribution($allScores);

        DailySummary::updateOrCreate(
            ['board_number' => $boardNumber],
            [
                'puzzle_date' => $puzzleDate,
                'participant_count' => $publicScores->count(),
                'wg_participant_count' => $allScores->count(),
                'wg_score_mean' => $wgScoreMean,
                'wg_score_distribution' => $wgDistribution,
                'score_mean' => $scoreMean,
                'score_median' => $scoreMedian,
                'score_distribution' => $distribution,
                'bot_skill_mean' => $botSkillMean,
                'bot_luck_mean' => $botLuckMean,
                'difficulty_delta' => $difficultyDelta,
                'all_time_mean' => $allTimeMean,
                'boards' => $boards,
            ]
        );
    }

    protected function getPublicScoresForBoard(int $boardNumber)
    {
        return Score::query()
            ->where('board_number', $boardNumber)
            ->whereHas('user', function ($query) {
                $query->where('show_on_public_leaderboard', true);
            })
            ->with('user')
            ->get()
            ->unique('user_id');
    }

    protected function getAllScoresForBoard(int $boardNumber)
    {
        return Score::query()
            ->where('board_number', $boardNumber)
            ->with('user')
            ->get()
            ->unique('user_id');
    }

    protected function getAllTimeMean(): ?float
    {
        $leaderboard = PublicLeaderboard::getForever();

        return $leaderboard?->score_mean;
    }

    protected function calculateMedian($scores): ?float
    {
        if ($scores->isEmpty()) {
            return null;
        }

        return round($scores->median(), 1);
    }

    protected function calculateDistribution($scores): array
    {
        return collect([1, 2, 3, 4, 5, 6, 'X'])
            ->mapWithKeys(function ($score) use ($scores) {
                $key = $score === 'X' ? 7 : $score;
                $count = $scores->where('score', $key)->count();
                return [$score => $count];
            })
            ->all();
    }

    protected function calculateBotSkillMean($scores): ?float
    {
        $scoresWithSkill = $scores->whereNotNull('bot_skill_score');

        if ($scoresWithSkill->isEmpty()) {
            return null;
        }

        return round($scoresWithSkill->avg('bot_skill_score'), 1);
    }

    protected function calculateBotLuckMean($scores): ?float
    {
        $scoresWithLuck = $scores->whereNotNull('bot_luck_score');

        if ($scoresWithLuck->isEmpty()) {
            return null;
        }

        return round($scoresWithLuck->avg('bot_luck_score'), 1);
    }

    protected function buildBoardsData($scores): array
    {
        return $scores->map(function ($score) {
            return [
                'user_id' => $score->user_id,
                'name' => $score->user->show_name_on_public_leaderboard
                    ? $score->user->public_display_name
                    : null,
                'score' => $score->score,
                'board' => $score->board,
                'hard_mode' => $score->hard_mode,
                'bot_skill' => $score->bot_skill_score,
                'bot_luck' => $score->bot_luck_score,
            ];
        })
        ->sortBy('score')
        ->values()
        ->all();
    }
}
