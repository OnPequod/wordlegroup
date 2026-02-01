<?php

namespace Database\Seeders;

use App\Models\Puzzle;
use Illuminate\Database\Seeder;

class PuzzleSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/puzzles.json');

        if (!file_exists($path)) {
            $this->command->error("Puzzle data file not found: {$path}");
            return;
        }

        $puzzles = json_decode(file_get_contents($path), true);

        if (empty($puzzles)) {
            $this->command->error('No puzzles found in data file');
            return;
        }

        $this->command->info('Importing ' . count($puzzles) . ' puzzles...');

        $bar = $this->command->getOutput()->createProgressBar(count($puzzles));
        $bar->start();

        foreach ($puzzles as $puzzle) {
            Puzzle::updateOrCreate(
                ['board_number' => $puzzle['board_number']],
                [
                    'puzzle_date' => $puzzle['puzzle_date'],
                    'answer' => $puzzle['answer'],
                    'editor' => $puzzle['editor'],
                ]
            );
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Puzzle import complete.');
    }
}
