<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\SubredditDTO;
use App\Models\Subreddit;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

final class SubredditService
{
    public function createSubreddit(SubredditDTO $subredditDTO): Subreddit
    {
        throw_if($this->subredditNameExists($subredditDTO->name), ValidationException::withMessages([
            'name' => ['Subreddit name already exists.'],
        ]));

        return Subreddit::query()->create($subredditDTO->toArray());
    }

    public function updateSubreddit(Subreddit $subreddit, SubredditDTO $subredditDTO): Subreddit
    {
        throw_if($subreddit->name !== $subredditDTO->name && $this->subredditNameExists($subredditDTO->name), ValidationException::withMessages([
            'name' => ['Subreddit name already exists.'],
        ]));

        $subreddit->update($subredditDTO->toArray());

        return $subreddit->fresh();
    }

    public function deleteSubreddit(Subreddit $subreddit): bool
    {
        return $subreddit->delete();
    }

    public function findSubreddit(int $id): ?Subreddit
    {
        return Subreddit::query()->find($id);
    }

    public function findSubredditByName(string $name): ?Subreddit
    {
        return Subreddit::query()->where('name', $name)->first();
    }

    public function getAllSubreddits(): Collection
    {
        return Subreddit::with('user')
            ->withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->get();
    }

    public function getPopularSubreddits(int $limit = 10): Collection
    {
        return Subreddit::popular()
            ->with('user')
            ->limit($limit)
            ->get();
    }

    public function getActiveSubreddits(): Collection
    {
        return Subreddit::active()
            ->with('user')
            ->withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->get();
    }

    public function getUserSubreddits(User $user): Collection
    {
        return $user->createdSubreddits()
            ->withCount('posts')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getSubredditPosts(Subreddit $subreddit, string $sort = 'hot', int $perPage = 20)
    {
        $query = $subreddit->posts()
            ->with(['user', 'subreddit'])
            ->withCount('comments');

        match ($sort) {
            'new' => $query->new(),
            'top' => $query->top(),
            default => $query->hot(),
        };

        return $query->paginate($perPage);
    }

    public function getSubredditStats(Subreddit $subreddit): array
    {
        return [
            'id' => $subreddit->id,
            'name' => $subreddit->name,
            'display_name' => $subreddit->display_name,
            'description' => $subreddit->description,
            'posts_count' => $subreddit->posts()->count(),
            'members_count' => $subreddit->posts()->distinct('user_id')->count('user_id'),
            'created_at' => $subreddit->created_at,
            'path' => $subreddit->path,
            'prefixed_name' => $subreddit->prefixed_name,
        ];
    }

    public function searchSubreddits(string $query, int $limit = 20): Collection
    {
        return Subreddit::query()->where('name', 'like', sprintf('%%%s%%', $query))
            ->orWhere('display_name', 'like', sprintf('%%%s%%', $query))
            ->withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Check if subreddit name already exists
     */
    private function subredditNameExists(string $name): bool
    {
        return Subreddit::query()->where('name', $name)->exists();
    }
}
