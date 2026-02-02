<?php

namespace Database\Factories;

use App\Concerns\WordleDate;
use App\Models\DailySummary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DailySummary>
 */
class DailySummaryFactory extends Factory
{
    public function definition(): array
    {
        $boardNumber = fake()->numberBetween(1000, 1500);

        return [
            'board_number' => $boardNumber,
            'puzzle_date' => app(WordleDate::class)->getDateFromBoardNumber($boardNumber),
            'participant_count' => fake()->numberBetween(10, 100),
            'score_mean' => fake()->randomFloat(2, 3.5, 4.5),
            'score_median' => fake()->numberBetween(3, 5),
            'score_distribution' => collect([
                1 => fake()->numberBetween(0, 5),
                2 => fake()->numberBetween(5, 15),
                3 => fake()->numberBetween(15, 30),
                4 => fake()->numberBetween(25, 40),
                5 => fake()->numberBetween(10, 20),
                6 => fake()->numberBetween(5, 15),
                'X' => fake()->numberBetween(0, 5),
            ]),
            'bot_skill_mean' => fake()->randomFloat(1, 50, 90),
            'bot_luck_mean' => fake()->randomFloat(1, 40, 80),
            'difficulty_delta' => fake()->randomFloat(2, -1, 1),
            'all_time_mean' => fake()->randomFloat(2, 3.8, 4.2),
            'boards' => null,
        ];
    }

    public function forBoardNumber(int $boardNumber): static
    {
        return $this->state(fn () => [
            'board_number' => $boardNumber,
            'puzzle_date' => app(WordleDate::class)->getDateFromBoardNumber($boardNumber),
        ]);
    }

    public function easy(): static
    {
        return $this->state(fn () => [
            'difficulty_delta' => fake()->randomFloat(2, -1.5, -0.5),
            'score_mean' => fake()->randomFloat(2, 3.2, 3.7),
        ]);
    }

    public function hard(): static
    {
        return $this->state(fn () => [
            'difficulty_delta' => fake()->randomFloat(2, 0.5, 1.5),
            'score_mean' => fake()->randomFloat(2, 4.3, 5.0),
        ]);
    }

    public function withBoards(array $boards): static
    {
        return $this->state(fn () => [
            'boards' => collect($boards),
        ]);
    }
}
