<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Model;
use App\Models\Post;
use App\Models\Subreddit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
final class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(random_int(5, 15)),
            'content' => fake()->optional(0.8)->paragraphs(random_int(1, 5), true),
            'user_id' => User::factory(),
            'subreddit_id' => Subreddit::factory(),
            'score' => fake()->numberBetween(-50, 1000),
        ];
    }

    /**
     * Create a post with specific content.
     */
    public function withContent(string $content): self
    {
        return $this->state(fn (array $attributes) => [
            'content' => $content,
        ]);
    }

    /**
     * Create a text-only post.
     */
    public function textOnly(): self
    {
        return $this->state(fn (array $attributes) => [
            'content' => fake()->paragraphs(random_int(2, 6), true),
        ]);
    }

    /**
     * Create a post with high score.
     */
    public function popular(): self
    {
        return $this->state(fn (array $attributes) => [
            'score' => fake()->numberBetween(100, 1000),
        ]);
    }

    /**
     * Create a post with low or negative score.
     */
    public function controversial(): self
    {
        return $this->state(fn (array $attributes) => [
            'score' => fake()->numberBetween(-100, 50),
        ]);
    }

    /**
     * Create a post for a specific subreddit.
     */
    public function forSubreddit(Subreddit $subreddit): self
    {
        return $this->state(fn (array $attributes) => [
            'subreddit_id' => $subreddit->id,
        ]);
    }

    /**
     * Create a post by a specific user.
     */
    public function byUser(User $user): self
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
