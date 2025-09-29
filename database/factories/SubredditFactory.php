<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Subreddit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Subreddit>
 */
final class SubredditFactory extends Factory
{
    protected $model = Subreddit::class;

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

    public function withName(string $name): self
    {
        return $this->state(fn (array $attributes) => [
            'name' => Str::slug($name),
            'display_name' => ucwords($name),
        ]);
    }

    public function popular(): self
    {
        return $this->state(fn (array $attributes) => [
            'description' => fake()->sentence(),
        ]);
    }
}
