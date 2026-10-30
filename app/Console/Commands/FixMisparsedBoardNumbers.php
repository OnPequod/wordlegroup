<?php

namespace App\Console\Commands;

use App\Concerns\WordleBoard;
use App\Concerns\WordleDate;
use App\Jobs\SyncScoreDataJob;
use App\Jobs\UpdateDailySummariesJob;
use App\Models\Score;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class FixMisparsedBoardNumbers extends Command
{
    /**
     * Shares like "Wordle 1.959 4/6" used to be parsed as board 959. A score
     * is treated as misparsed when it was recorded from a shared board, its
     * board number is below 1,000, and it was recorded within a couple of
     * days of the board 1,000 higher.
     */
    protected $signature = 'scores:fix-misparsed-board-numbers
                            {--dry-run : List the scores that would be moved without changing them}
                            {--window=2 : Days either side of the corrected board a score may have been recorded}';

    protected $description = 'Move scores whose thousands separator was misparsed to their real board';

    public function handle(): int
    {
        $scores = $this->misparsedScores();

        if ($scores->isEmpty()) {
            $this->info('No misparsed scores found.');

            return self::SUCCESS;
        }

        $this->table(
            ['Score', 'User', 'Recorded', 'From board', 'To board'],
            $scores->map(fn (Score $score) => [
                $score->id,
                $score->user_id,
                $score->created_at->toDateTimeString(),
                $score->board_number,
                $score->board_number + 1000,
            ])
        );

        if ($this->option('dry-run')) {
            $this->info("{$scores->count()} scores would be moved.");

            return self::SUCCESS;
        }

        $scores->each(fn (Score $score) => $this->move($score));

        $this->info("{$scores->count()} scores moved.");

        return self::SUCCESS;
    }

    /**
     * @return Collection<int, Score>
     */
    protected function misparsedScores(): Collection
    {
        $wordleBoard = app(WordleBoard::class);
        $window = (int) $this->option('window');

        return Score::query()
            ->whereNotNull('board')
            ->where('board_number', '<', 1000)
            ->where('created_at', '>=', app(WordleDate::class)->getDateFromBoardNumber(1000 - $window))
            ->orderBy('id')
            ->get()
            ->filter(function (Score $score) use ($wordleBoard, $window): bool {
                $correctedBoardNumber = $score->board_number + 1000;
                $recordedOnBoardNumber = $wordleBoard->getBoardNumberFromDate($score->created_at)
                    ?? $wordleBoard->activeBoardNumber;

                return $correctedBoardNumber <= $wordleBoard->activeBoardNumber
                    && abs($recordedOnBoardNumber - $correctedBoardNumber) <= $window;
            })
            ->values();
    }

    protected function move(Score $score): void
    {
        $oldBoardNumber = $score->board_number;
        $newBoardNumber = $oldBoardNumber + 1000;

        DB::transaction(function () use ($score, $newBoardNumber): void {
            $score->user->dailyScores()->detach($score->id);
            $score->user->recordedGroupMembershipScores()->detach($score->id);

            $score->update([
                'board_number' => $newBoardNumber,
                'date' => app(WordleDate::class)->getDateFromBoardNumber($newBoardNumber)->format('Y-m-d'),
            ]);
        });

        UpdateDailySummariesJob::dispatch($oldBoardNumber);

        $remainingScore = Score::query()
            ->where('user_id', $score->user_id)
            ->where('board_number', $oldBoardNumber)
            ->latest('id')
            ->first();

        if ($remainingScore) {
            SyncScoreDataJob::dispatch($remainingScore);
        }
    }
}
