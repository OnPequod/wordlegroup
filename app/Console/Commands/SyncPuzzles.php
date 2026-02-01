<?php

namespace App\Console\Commands;

use App\Concerns\WordleDate;
use App\Models\Puzzle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncPuzzles extends Command
{
    protected $signature = 'puzzles:sync
                            {--days=7 : Number of recent days to sync}
                            {--all : Sync all puzzles from the beginning}';

    protected $description = 'Sync Wordle puzzle answers from NYT API';

    public function handle(): int
    {
        if ($this->option('all')) {
            return $this->syncAll();
        }

        return $this->syncRecent((int) $this->option('days'));
    }

    protected function syncRecent(int $days): int
    {
        $this->info("Syncing last {$days} days from NYT...");

        $synced = 0;
        $failed = 0;

        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($i)->format('Y-m-d');
            $result = $this->fetchAndStorePuzzle($date);

            if ($result) {
                $synced++;
            } else {
                $failed++;
            }
        }

        $this->info("Done: {$synced} synced, {$failed} failed");

        return Command::SUCCESS;
    }

    protected function syncAll(): int
    {
        $this->info('Syncing all puzzles from NYT (this will take a while)...');

        $wordleDate = app(WordleDate::class);
        $startDate = $wordleDate->getFirstBoardStartTime();
        $endDate = now();

        $totalDays = $startDate->diffInDays($endDate) + 1;
        $bar = $this->output->createProgressBar($totalDays);
        $bar->start();

        $synced = 0;
        $failed = 0;
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $date = $currentDate->format('Y-m-d');
            $result = $this->fetchAndStorePuzzle($date);

            if ($result) {
                $synced++;
            } else {
                $failed++;
            }

            $bar->advance();
            $currentDate->addDay();

            // Rate limit: 100ms between requests
            usleep(100000);
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done: {$synced} synced, {$failed} failed");

        return Command::SUCCESS;
    }

    protected function fetchAndStorePuzzle(string $date): bool
    {
        try {
            $response = Http::timeout(10)->get(
                "https://www.nytimes.com/svc/wordle/v2/{$date}.json"
            );

            if (!$response->successful()) {
                return false;
            }

            $data = $response->json();

            if (!isset($data['solution']) || !isset($data['days_since_launch'])) {
                return false;
            }

            Puzzle::updateOrCreate(
                ['board_number' => $data['days_since_launch']],
                [
                    'puzzle_date' => $data['print_date'],
                    'answer' => strtoupper($data['solution']),
                    'editor' => $data['editor'] ?? null,
                ]
            );

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
