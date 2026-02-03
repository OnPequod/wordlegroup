<?php

namespace App\Services;

use App\Models\Group;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Collection;

class ScoreExportService
{
    /**
     * Parse board emoji string into 6 lines.
     *
     * @param string|null $board
     * @return array<int, string>
     */
    public function parseBoardLines(?string $board): array
    {
        if ($board === null || $board === '') {
            return array_fill(0, 6, '');
        }

        $lines = preg_split('/\r?\n/', $board);
        return array_pad(array_slice($lines, 0, 6), 6, '');
    }

    /**
     * Get CSV headers for account export.
     *
     * @return array<int, string>
     */
    public function getAccountHeaders(): array
    {
        return [
            'date',
            'board_number',
            'score',
            'hard_mode',
            'skill_score',
            'luck_score',
            'line_1',
            'line_2',
            'line_3',
            'line_4',
            'line_5',
            'line_6',
        ];
    }

    /**
     * Get CSV headers for group export.
     *
     * @return array<int, string>
     */
    public function getGroupHeaders(): array
    {
        return [
            'member_id',
            'name',
            'date',
            'board_number',
            'score',
            'hard_mode',
            'skill_score',
            'luck_score',
            'line_1',
            'line_2',
            'line_3',
            'line_4',
            'line_5',
            'line_6',
        ];
    }

    /**
     * Transform a score to an account export row.
     *
     * @param Score $score
     * @return array<int, mixed>
     */
    public function scoreToAccountRow(Score $score): array
    {
        $lines = $this->parseBoardLines($score->getRawOriginal('board'));

        return [
            $score->date?->format('Y-m-d'),
            $score->board_number,
            $score->score === 7 ? 'X' : $score->score,
            $score->hard_mode ? 'true' : 'false',
            $score->bot_skill_score,
            $score->bot_luck_score,
            $lines[0],
            $lines[1],
            $lines[2],
            $lines[3],
            $lines[4],
            $lines[5],
        ];
    }

    /**
     * Transform a score to a group export row.
     *
     * @param Score $score
     * @param int $memberId
     * @param string $name
     * @return array<int, mixed>
     */
    public function scoreToGroupRow(Score $score, int $memberId, string $name): array
    {
        $lines = $this->parseBoardLines($score->getRawOriginal('board'));

        return [
            $memberId,
            $name,
            $score->date?->format('Y-m-d'),
            $score->board_number,
            $score->score === 7 ? 'X' : $score->score,
            $score->hard_mode ? 'true' : 'false',
            $score->bot_skill_score,
            $score->bot_luck_score,
            $lines[0],
            $lines[1],
            $lines[2],
            $lines[3],
            $lines[4],
            $lines[5],
        ];
    }

    /**
     * Build a mapping of user_id to member_id based on join order.
     *
     * @param Group $group
     * @return Collection<int, int>
     */
    public function getMemberIdMap(Group $group): Collection
    {
        return $group->memberships()
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->pluck('user_id')
            ->mapWithKeys(fn($userId, $index) => [$userId => $index + 1]);
    }

    /**
     * Stream user's scores to CSV output.
     *
     * @param User $user
     * @param resource $output
     */
    public function streamAccountCsv(User $user, $output): void
    {
        fputcsv($output, $this->getAccountHeaders());

        Score::query()
            ->where('user_id', $user->id)
            ->orderBy('board_number', 'asc')
            ->chunk(500, function ($scores) use ($output) {
                foreach ($scores as $score) {
                    fputcsv($output, $this->scoreToAccountRow($score));
                }
            });
    }

    /**
     * Stream group scores to CSV output.
     *
     * @param Group $group
     * @param resource $output
     */
    public function streamGroupCsv(Group $group, $output): void
    {
        fputcsv($output, $this->getGroupHeaders());

        $memberIdMap = $this->getMemberIdMap($group);

        // Build name map from memberships
        $nameMap = $group->memberships()
            ->with('user')
            ->get()
            ->pluck('user.name', 'user_id');

        $group->scores()
            ->orderBy('scores.board_number', 'asc')
            ->orderBy('scores.user_id', 'asc')
            ->chunk(500, function ($scores) use ($output, $memberIdMap, $nameMap) {
                foreach ($scores as $score) {
                    $memberId = $memberIdMap[$score->user_id] ?? 0;
                    $name = $nameMap[$score->user_id] ?? 'Unknown';
                    fputcsv($output, $this->scoreToGroupRow($score, $memberId, $name));
                }
            });
    }
}
