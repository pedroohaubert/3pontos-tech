<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Subreddit;
use App\Models\User;

final class SubredditPolicy
{
    public function viewAny(): bool
    {
        return true;
        // Anyone can view the list of subreddits
    }

    public function view(): bool
    {
        return true;
        // Anyone can view a subreddit
    }

    public function create(): bool
    {
        return true;
        // Any authenticated user can create subreddits
    }

    public function update(User $user, Subreddit $subreddit): bool
    {
        return $user->id === $subreddit->user_id || $user->isAdmin();
    }

    public function delete(User $user, Subreddit $subreddit): bool
    {
        return $user->id === $subreddit->user_id || $user->isAdmin();
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
