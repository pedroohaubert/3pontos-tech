<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Model;
use App\Models\Subreddit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Subreddit>
 */
final class SubredditFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = Subreddit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $displayName = fake()->words(random_int(1, 3), true);
        $name = Str::slug($displayName);

        return [
            'name' => $name,
            'display_name' => ucwords($displayName),
            'description' => fake()->optional(0.7)->sentence(),
            'user_id' => User::factory(),
        ];
    }

    /**
     * Create a subreddit with a specific name.
     */
    public function withName(string $name): self
    {
        return $this->state(fn (array $attributes) => [
            'name' => Str::slug($name),
            'display_name' => ucwords($name),
        ]);
    }

    /**
     * Create a popular subreddit.
     */
    public function popular(): self
    {
        return $this->state(fn (array $attributes) => [
            'description' => fake()->sentence(),
        ]);
    }
}
