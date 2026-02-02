<?php

namespace Database\Factories;

use App\Concerns\WordleDate;
use App\Models\Puzzle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Puzzle>
 */
class PuzzleFactory extends Factory
{
    public function definition(): array
    {
        $boardNumber = fake()->numberBetween(1000, 1500);
        $words = ['CRANE', 'SLATE', 'STARE', 'TRACE', 'CRATE', 'RAISE', 'ARISE', 'IRATE', 'AROSE', 'AUDIO'];

        return [
            'board_number' => $boardNumber,
            'puzzle_date' => app(WordleDate::class)->getDateFromBoardNumber($boardNumber),
            'answer' => fake()->randomElement($words),
            'editor' => 'Tracy Bennett',
        ];
    }

    public function forBoardNumber(int $boardNumber): static
    {
        return $this->state(fn () => [
            'board_number' => $boardNumber,
            'puzzle_date' => app(WordleDate::class)->getDateFromBoardNumber($boardNumber),
        ]);
    }

    public function withAnswer(string $answer): static
    {
        return $this->state(fn () => [
            'answer' => strtoupper($answer),
        ]);
    }
}
