<?php

namespace App\Concerns;

use App\Models\DailySummary;
use App\Models\PublicLeaderboard;
use App\Models\Score;
use Carbon\Carbon;

class UpdatesDailySummaries
{
    /**
     * Update today and yesterday's summaries (for scheduled task).
     */
    public function updateRecent(): void
    {
        $wordleDate = app(WordleDate::class);
        $today = $wordleDate->activeBoardNumber;
        $yesterday = $today - 1;

        $this->updateForBoardNumber($today);

        if ($yesterday >= 0) {
            $this->updateForBoardNumber($yesterday);
        }
    }

    /**
     * Rebuild all summaries from scratch.
     */
    public function rebuildAll(): void
    {
        $wordleDate = app(WordleDate::class);
        $currentBoard = $wordleDate->activeBoardNumber;

        // Get the earliest board with scores
        $earliestBoard = Score::min('board_number') ?? 0;

        for ($board = $earliestBoard; $board <= $currentBoard; $board++) {
            $this->updateForBoardNumber($board);
        }
    }

    /**
     * Update summary for a specific board number.
     */
    public function updateForBoardNumber(int $boardNumber): void
    {
        $wordleDate = app(WordleDate::class);
        $puzzleDate = $wordleDate->getDateFromBoardNumber($boardNumber);

        // Get all-time mean from PublicLeaderboard for difficulty calculation
        $foreverLeaderboard = PublicLeaderboard::getForever();
        $allTimeMean = $foreverLeaderboard?->score_mean;

        // Query scores for this board from opted-in users
        $publicScores = Score::where('board_number', $boardNumber)
            ->whereHas('user', fn($q) => $q->where('show_on_public_leaderboard', true))
            ->with('user')
            ->get();

        // Query all WG scores for this board (for internal stats)
        $allScores = Score::where('board_number', $boardNumber)->get();

        $participantCount = $publicScores->count();
        $scoreMean = $publicScores->isNotEmpty() ? round($publicScores->average('score'), 2) : null;
        $scoreMedian = $publicScores->isNotEmpty() ? round($publicScores->median('score'), 1) : null;

        // Calculate distribution for public scores
        $scoreDistribution = collect([1, 2, 3, 4, 5, 6, 7])
            ->mapWithKeys(fn($score) => [
                ($score === 7 ? 'X' : $score) => $publicScores->where('score', $score)->count(),
            ]);

        // Calculate WG distribution (all users)
        $wgScoreDistribution = collect([1, 2, 3, 4, 5, 6, 7])
            ->mapWithKeys(fn($score) => [
                ($score === 7 ? 'X' : $score) => $allScores->where('score', $score)->count(),
            ]);

        // Bot scores from public users
        $botSkillScores = $publicScores->whereNotNull('bot_skill_score');
        $botLuckScores = $publicScores->whereNotNull('bot_luck_score');

        $botSkillMean = $botSkillScores->isNotEmpty()
            ? round($botSkillScores->average('bot_skill_score'), 1)
            : null;
        $botLuckMean = $botLuckScores->isNotEmpty()
            ? round($botLuckScores->average('bot_luck_score'), 1)
            : null;

        // Difficulty delta (positive = harder than average) - based on ALL WG users
        $wgScoreMean = $allScores->isNotEmpty() ? round($allScores->average('score'), 2) : null;
        $difficultyDelta = ($wgScoreMean !== null && $allTimeMean !== null)
            ? round($wgScoreMean - $allTimeMean, 2)
            : null;

        // Build boards array for opted-in users
        $boards = $publicScores
            ->map(function ($score) {
                $showName = $score->user->show_name_on_public_leaderboard;

                return [
                    'user_id' => $score->user_id,
                    'name' => $showName ? $score->user->public_display_name : null,
                    'show_name' => $showName,
                    'score' => $score->score,
                    'board' => $score->board,
                    'hard_mode' => $score->hard_mode,
                    'bot_skill' => $score->bot_skill_score,
                    'bot_luck' => $score->bot_luck_score,
                ];
            })
            ->sortBy('score')
            ->values();

        DailySummary::updateOrCreate(
            ['board_number' => $boardNumber],
            [
                'puzzle_date' => $puzzleDate->toDateString(),
                'participant_count' => $participantCount,
                'score_mean' => $scoreMean,
                'score_median' => $scoreMedian,
                'score_distribution' => $scoreDistribution,
                'wg_score_distribution' => $wgScoreDistribution,
                'bot_skill_mean' => $botSkillMean,
                'bot_luck_mean' => $botLuckMean,
                'difficulty_delta' => $difficultyDelta,
                'all_time_mean' => $allTimeMean,
                'boards' => $boards,
                'wg_participant_count' => $allScores->count(),
                'wg_score_mean' => $allScores->isNotEmpty() ? round($allScores->average('score'), 2) : null,
            ]
        );
    }
}
