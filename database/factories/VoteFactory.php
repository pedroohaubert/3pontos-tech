<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vote>
 */
final class VoteFactory extends Factory
{
    protected $model = Vote::class;

    public function definition(): array
    {
        $voteable = fake()->randomElement([
            Post::factory()->create(),
            Comment::factory()->create(),
        ]);

        return [
            'user_id' => User::factory(),
            'voteable_type' => $voteable::class,
            'voteable_id' => $voteable->id,
            'type' => fake()->randomElement(['up', 'down']),
        ];
    }

    public function upvote(): self
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'up',
        ]);
    }

    public function downvote(): self
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'down',
        ]);
    }

    public function forPost(Post $post): self
    {
        return $this->state(fn (array $attributes) => [
            'voteable_type' => Post::class,
            'voteable_id' => $post->id,
        ]);
    }

    public function forComment(Comment $comment): self
    {
        return $this->state(fn (array $attributes) => [
            'voteable_type' => Comment::class,
            'voteable_id' => $comment->id,
        ]);
    }

    public function byUser(User $user): self
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    public function postsOnly(): self
    {
        $post = Post::factory()->create();

        return $this->state(fn (array $attributes) => [
            'voteable_type' => Post::class,
            'voteable_id' => $post->id,
        ]);
    }

    public function commentsOnly(): self
    {
        $comment = Comment::factory()->create();

        return $this->state(fn (array $attributes) => [
            'voteable_type' => Comment::class,
            'voteable_id' => $comment->id,
        ]);
    }
}
