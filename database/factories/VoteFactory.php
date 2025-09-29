<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Model;
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
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = Vote::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Randomly choose between Post and Comment
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

    /**
     * Create an upvote.
     */
    public function upvote(): self
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'up',
        ]);
    }

    /**
     * Create a downvote.
     */
    public function downvote(): self
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'down',
        ]);
    }

    /**
     * Create a vote for a specific post.
     */
    public function forPost(Post $post): self
    {
        return $this->state(fn (array $attributes) => [
            'voteable_type' => Post::class,
            'voteable_id' => $post->id,
        ]);
    }

    /**
     * Create a vote for a specific comment.
     */
    public function forComment(Comment $comment): self
    {
        return $this->state(fn (array $attributes) => [
            'voteable_type' => Comment::class,
            'voteable_id' => $comment->id,
        ]);
    }

    /**
     * Create a vote by a specific user.
     */
    public function byUser(User $user): self
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create votes only for posts.
     */
    public function postsOnly(): self
    {
        $post = Post::factory()->create();

        return $this->state(fn (array $attributes) => [
            'voteable_type' => Post::class,
            'voteable_id' => $post->id,
        ]);
    }

    /**
     * Create votes only for comments.
     */
    public function commentsOnly(): self
    {
        $comment = Comment::factory()->create();

        return $this->state(fn (array $attributes) => [
            'voteable_type' => Comment::class,
            'voteable_id' => $comment->id,
        ]);
    }
}
