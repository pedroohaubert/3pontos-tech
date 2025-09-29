<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

final class CommentPolicy
{
    public function viewAny(): bool
    {
        return true;
        // Anyone can view comments
    }

    public function view(): bool
    {
        return true;
        // Anyone can view a comment
    }

    public function create(): bool
    {
        return true;
        // Any authenticated user can create comments
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->isAdmin();
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->isAdmin();
    }

    public function restore(User $user): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user): bool
    {
        return $user->isAdmin();
    }
}
