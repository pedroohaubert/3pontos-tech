<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Model;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
final class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => fake()->paragraphs(random_int(1, 3), true),
            'user_id' => User::factory(),
            'post_id' => Post::factory(),
            'parent_id' => null, // Root comment by default
            'score' => fake()->numberBetween(-10, 100),
            'depth' => 0, // Will be auto-calculated by model event
        ];
    }

    /**
     * Create a reply to a specific comment.
     */
    public function replyTo(Comment $parent): self
    {
        return $this->state(fn (array $attributes) => [
            'post_id' => $parent->post_id,
            'parent_id' => $parent->id,
            'depth' => $parent->depth + 1,
        ]);
    }

    /**
     * Create a root comment (no parent).
     */
    public function root(): self
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => null,
            'depth' => 0,
        ]);
    }

    /**
     * Create a nested reply at a specific depth.
     */
    public function atDepth(int $depth): self
    {
        return $this->state(fn (array $attributes) => [
            'depth' => $depth,
        ]);
    }

    /**
     * Create a comment on a specific post.
     */
    public function onPost(Post $post): self
    {
        return $this->state(fn (array $attributes) => [
            'post_id' => $post->id,
        ]);
    }

    /**
     * Create a comment by a specific user.
     */
    public function byUser(User $user): self
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create a highly upvoted comment.
     */
    public function popular(): self
    {
        return $this->state(fn (array $attributes) => [
            'score' => fake()->numberBetween(50, 500),
        ]);
    }

    /**
     * Create a short comment.
     */
    public function short(): self
    {
        return $this->state(fn (array $attributes) => [
            'content' => fake()->sentence(random_int(3, 8)),
        ]);
    }

    /**
     * Create a long comment.
     */
    public function long(): self
    {
        return $this->state(fn (array $attributes) => [
            'content' => fake()->paragraphs(random_int(4, 8), true),
        ]);
    }
}
