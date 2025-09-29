<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

final class VotePolicy
{
    public function voteOnPost(User $user, Post $post): bool
    {
        // Users cannot vote on their own posts
        return $user->id !== $post->user_id;
    }

    public function voteOnComment(User $user, Comment $comment): bool
    {
        // Users cannot vote on their own comments
        return $user->id !== $comment->user_id;
    }

    public function create(): bool
    {
        return true;
        // Any authenticated user can vote
    }

    public function delete(): bool
    {
        return true;
        // Any authenticated user can remove their own votes
    }
}
