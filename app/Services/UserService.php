<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\UserDTO;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class UserService
{
    public function createUser(UserDTO $userDTO): User
    {
        return User::query()->create($userDTO->toArray());
    }

    public function updateUser(User $user, UserDTO $userDTO): User
    {
        $user->update($userDTO->toArray());

        return $user->fresh();
    }

    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }

    public function findUser(int $id): ?User
    {
        return User::query()->find($id);
    }

    public function findUserByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    public function getAllUsers(): Collection
    {
        return User::all();
    }

    public function updateUserRole(User $user, string $role): User
    {
        $user->update(['role' => $role]);

        return $user->fresh();
    }

    public function canAccessAdminPanel(User $user): bool
    {
        return $user->isAdmin();
    }

    public function getUserProfile(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'created_at' => $user->created_at,
            'posts_count' => $user->posts()->count(),
            'comments_count' => $user->comments()->count(),
            'subreddits_count' => $user->createdSubreddits()->count(),
            'votes_count' => $user->votes()->count(),
        ];
    }

    public function getUserPosts(User $user, int $perPage = 20)
    {
        return $user->posts()
            ->with(['subreddit', 'user'])
            ->withCount('comments')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getUserComments(User $user, int $perPage = 20)
    {
        return $user->comments()
            ->with(['post.subreddit', 'post.user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getUserSubreddits(User $user): Collection
    {
        return $user->createdSubreddits()
            ->withCount('posts')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
