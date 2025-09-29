<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
final class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'content' => fake()->paragraphs(random_int(1, 3), true),
            'user_id' => User::factory(),
            'post_id' => Post::factory(),
            'parent_id' => null,
            'score' => fake()->numberBetween(-10, 100),
            'depth' => 0,
        ];
    }

    public function replyTo(Comment $parent): self
    {
        return $this->state(fn (array $attributes) => [
            'post_id' => $parent->post_id,
            'parent_id' => $parent->id,
            'depth' => $parent->depth + 1,
        ]);
    }

    public function root(): self
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => null,
            'depth' => 0,
        ]);
    }

    public function atDepth(int $depth): self
    {
        return $this->state(fn (array $attributes) => [
            'depth' => $depth,
        ]);
    }

    public function onPost(Post $post): self
    {
        return $this->state(fn (array $attributes) => [
            'post_id' => $post->id,
        ]);
    }

    public function byUser(User $user): self
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    public function popular(): self
    {
        return $this->state(fn (array $attributes) => [
            'score' => fake()->numberBetween(50, 500),
        ]);
    }

    public function short(): self
    {
        return $this->state(fn (array $attributes) => [
            'content' => fake()->sentence(random_int(3, 8)),
        ]);
    }

    public function long(): self
    {
        return $this->state(fn (array $attributes) => [
            'content' => fake()->paragraphs(random_int(4, 8), true),
        ]);
    }
}
