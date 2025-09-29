<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\VoteDTO;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

final class VoteService
{
    public function castVote(User $user, Model $voteable, string $type): ?Vote
    {
        $this->validateVoteType($type);
        $this->validateVoteable($voteable);
        $this->preventSelfVoting($user, $voteable);

        return Vote::toggle($user, $voteable, $type);
    }

    public function upvote(User $user, Model $voteable): ?Vote
    {
        return $this->castVote($user, $voteable, VoteDTO::TYPE_UP);
    }

    public function downvote(User $user, Model $voteable): ?Vote
    {
        return $this->castVote($user, $voteable, VoteDTO::TYPE_DOWN);
    }

    public function removeVote(User $user, Model $voteable): bool
    {
        $this->validateVoteable($voteable);

        return Vote::removeVote($user, $voteable);
    }

    public function hasUserVoted(User $user, Model $voteable, ?string $type = null): bool
    {
        $this->validateVoteable($voteable);

        return Vote::hasVoted($user, $voteable, $type);
    }

    public function getUserVoteType(User $user, Model $voteable): ?string
    {
        $this->validateVoteable($voteable);

        return Vote::getUserVote($user, $voteable);
    }

    public function getVoteStats(Model $voteable): array
    {
        $this->validateVoteable($voteable);

        return [
            'score' => $voteable->score,
            'upvotes_count' => $voteable->votes()->upvotes()->count(),
            'downvotes_count' => $voteable->votes()->downvotes()->count(),
            'total_votes' => $voteable->votes()->count(),
        ];
    }

    public function getUserVotes(User $user, ?string $type = null, int $perPage = 20)
    {
        $query = $user->votes()->with(['voteable']);

        if ($type !== null) {
            $query->where('type', $type);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getPostVotes(Post $post, ?string $type = null): array
    {
        $query = $post->votes()->with(['user']);

        if ($type !== null) {
            $query->where('type', $type);
        }

        return $query->orderBy('created_at', 'desc')->get()->toArray();
    }

    public function getCommentVotes(Comment $comment, ?string $type = null): array
    {
        $query = $comment->votes()->with(['user']);

        if ($type !== null) {
            $query->where('type', $type);
        }

        return $query->orderBy('created_at', 'desc')->get()->toArray();
    }

    public function getUserVotingHistory(User $user, int $perPage = 20)
    {
        return $user->votes()
            ->with([
                'voteable' => function ($query): void {
                    $query->morphWith([
                        Post::class => ['subreddit'],
                        Comment::class => ['post.subreddit'],
                    ]);
                },
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function calculateUserKarma(User $user): int
    {
        $postScore = $user->posts()->sum('score');
        $commentScore = $user->comments()->sum('score');

        return $postScore + $commentScore;
    }

    public function getUserVotingStats(User $user): array
    {
        $totalVotes = $user->votes()->count();
        $upvotes = $user->votes()->upvotes()->count();
        $downvotes = $user->votes()->downvotes()->count();

        return [
            'total_votes' => $totalVotes,
            'upvotes' => $upvotes,
            'downvotes' => $downvotes,
            'karma' => $this->calculateUserKarma($user),
        ];
    }

    public function toggleVote(User $user, Model $voteable, string $type): ?Vote
    {
        return $this->castVote($user, $voteable, $type);
    }

    public function getTopVotedPostsInSubreddit(int $subredditId, int $limit = 10)
    {
        return Post::bySubreddit($subredditId)
            ->with(['user', 'subreddit'])
            ->withCount('comments')
            ->top()
            ->limit($limit)
            ->get();
    }

    public function getTopVotedCommentsInPost(int $postId, int $limit = 10)
    {
        return Comment::byPost($postId)
            ->with(['user', 'post'])
            ->withCount('children')
            ->top()
            ->limit($limit)
            ->get();
    }

    private function validateVoteType(string $type): void
    {
        throw_unless(in_array($type, VoteDTO::getAllowedTypes(), true), ValidationException::withMessages([
            'type' => ['Invalid vote type. Must be "up" or "down".'],
        ]));
    }

    private function validateVoteable(Model $voteable): void
    {
        throw_if(! $voteable instanceof Post && ! $voteable instanceof Comment, ValidationException::withMessages([
            'voteable' => ['Invalid voteable model. Must be a Post or Comment.'],
        ]));
    }

    private function preventSelfVoting(User $user, Model $voteable): void
    {
        throw_if($voteable->user_id === $user->id, ValidationException::withMessages([
            'vote' => ['You cannot vote on your own content.'],
        ]));
    }
}
