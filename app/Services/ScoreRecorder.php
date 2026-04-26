<?php

namespace App\Services;

use App\Concerns\WordleBoard;
use App\Models\Score;
use App\Models\User;
use Carbon\Carbon;

class ScoreRecorder
{
    public function record(User $user, array $data, ?User $recordingUser = null): Score
    {
        $date = Carbon::parse($data['date']);

        return Score::create([
            'user_id' => $user->id,
            'recording_user_id' => ($recordingUser ?? $user)->id,
            'date' => $date->format('Y-m-d'),
            'score' => $data['score'],
            'board_number' => $data['boardNumber'],
            'board' => $data['board'] ?? null,
            'hard_mode' => $data['hardMode'] ?? null,
            'bot_skill_score' => $data['botSkillScore'] ?? null,
            'bot_luck_score' => $data['botLuckScore'] ?? null,
        ]);
    }

    public function recordFromBoard(User $user, string $board, ?User $recordingUser = null): Score
    {
        $data = app(WordleBoard::class)->parse($board);

        return $this->record($user, [
            'score' => $data['scoreNumber'],
            'boardNumber' => $data['boardNumber'],
            'date' => $data['date'],
            'board' => $data['board'],
            'hardMode' => $data['hardMode'] ?? null,
            'botSkillScore' => $data['botScores']['skill'] ?? null,
            'botLuckScore' => $data['botScores']['luck'] ?? null,
        ], $recordingUser);
    }
}
