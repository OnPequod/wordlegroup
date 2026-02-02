<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Group;
use App\Models\Score;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'group_id' => Group::factory(),
            'body' => $this->faker->paragraph(),
        ];
    }

    public function forScore(Score $score): static
    {
        return $this->state(fn (array $attributes) => [
            'commentable_type' => Score::class,
            'commentable_id' => $score->id,
        ]);
    }

    public function discussionPost(): static
    {
        return $this->state(fn (array $attributes) => [
            'commentable_type' => null,
            'commentable_id' => null,
        ]);
    }

    public function reply(Comment $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
            'group_id' => $parent->group_id,
            'commentable_type' => $parent->commentable_type,
            'commentable_id' => $parent->commentable_id,
        ]);
    }
}
