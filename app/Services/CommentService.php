<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CommentDTO;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class CommentService
{
    public function createComment(CommentDTO $commentDTO): Comment
    {
        $comment = Comment::query()->create($commentDTO->toArray());

        return $comment->load(['user', 'post']);
    }

    public function updateComment(Comment $comment, CommentDTO $commentDTO): Comment
    {
        $comment->update($commentDTO->toArray());

        return $comment->fresh(['user', 'post']);
    }

    public function deleteComment(Comment $comment): bool
    {
        return $comment->delete();
    }

    public function forceDeleteComment(Comment $comment): bool
    {
        return $comment->forceDelete();
    }

    public function restoreComment(Comment $comment): bool
    {
        return $comment->restore();
    }

    public function findComment(int $id): ?Comment
    {
        return Comment::with(['user', 'post', 'parent'])->find($id);
    }

    public function createReply(Comment $parentComment, CommentDTO $commentDTO): Comment
    {
        $commentDTO = new CommentDTO(
            content: $commentDTO->content,
            userId: $commentDTO->userId,
            postId: $parentComment->post_id,
            parentId: $parentComment->id,
            depth: $parentComment->depth + 1,
        );

        return $this->createComment($commentDTO);
    }

    public function getPostComments(Post $post, bool $includeNested = true): Collection
    {
        if ($includeNested) {
            return $this->getNestedComments($post);
        }

        return $post->comments()
            ->root()
            ->with(['user'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getUserComments(User $user, int $perPage = 20)
    {
        return $user->comments()
            ->with(['post.subreddit', 'post.user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getCommentThread(Comment $comment): array
    {
        return $comment->getThread();
    }

    public function getCommentWithContext(int $commentId): ?Comment
    {
        return Comment::with([
            'user',
            'post.subreddit',
            'post.user',
            'parent.user',
            'children' => function ($query): void {
                $query->with(['user', 'children.user'])
                    ->orderBy('created_at', 'asc');
            },
        ])->find($commentId);
    }

    public function getCommentReplies(Comment $comment, bool $includeNested = true): Collection
    {
        $query = $comment->children()->with(['user']);

        if ($includeNested) {
            $query->with([
                'children' => function ($subQuery): void {
                    $subQuery->with(['user', 'children.user'])
                        ->orderBy('created_at', 'asc');
                },
            ]);
        }

        return $query->orderBy('created_at', 'asc')->get();
    }

    public function canUserEditComment(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id;
    }

    public function canUserDeleteComment(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id || $user->isAdmin();
    }

    public function updateCommentScore(Comment $comment): void
    {
        $comment->updateScore();
    }

    public function getCommentStats(Comment $comment): array
    {
        return [
            'id' => $comment->id,
            'score' => $comment->score,
            'depth' => $comment->depth,
            'is_reply' => $comment->isReply(),
            'replies_count' => $comment->reply_count,
            'upvotes_count' => $comment->upvote_count,
            'downvotes_count' => $comment->downvote_count,
            'created_at' => $comment->created_at,
            'time_ago' => $comment->time_ago,
        ];
    }

    public function searchComments(string $query, ?Post $post = null, int $perPage = 20)
    {
        $commentsQuery = Comment::with(['user', 'post.subreddit'])
            ->where('content', 'like', sprintf('%%%s%%', $query));

        if ($post instanceof Post) {
            $commentsQuery->byPost($post->id);
        }

        return $commentsQuery->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getRecentComments(int $limit = 20): Collection
    {
        return Comment::with(['user', 'post.subreddit'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function validateCommentDepth(?Comment $parentComment = null): bool
    {
        if (! $parentComment instanceof Comment) {
            return true; // Root comments are always allowed
        }

        $maxDepth = 5; // Configurable maximum depth

        return $parentComment->depth < $maxDepth;
    }

    private function getNestedComments(Post $post): Collection
    {
        return $post->comments()
            ->root()
            ->with([
                'user',
                'children' => function ($query): void {
                    $query->with(['user', 'children.user'])
                        ->orderBy('created_at', 'asc');
                },
            ])
            ->withCount('children')
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
