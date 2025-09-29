<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\PostDTO;
use App\Models\Post;
use App\Models\Subreddit;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service class for Post business logic
 */
final class PostService
{
    public function createPost(PostDTO $postDTO): Post
    {
        $post = Post::query()->create($postDTO->toArray());

        return $post->load(['user', 'subreddit']);
    }

    public function updatePost(Post $post, PostDTO $postDTO): Post
    {
        $post->update($postDTO->toArray());

        return $post->fresh(['user', 'subreddit']);
    }

    public function deletePost(Post $post): bool
    {
        return $post->delete();
    }

    public function forceDeletePost(Post $post): bool
    {
        return $post->forceDelete();
    }

    public function restorePost(Post $post): bool
    {
        return $post->restore();
    }

    public function findPost(int $id): ?Post
    {
        return Post::with(['user', 'subreddit'])->find($id);
    }

    public function getAllPosts(string $sort = 'hot', int $perPage = 20): LengthAwarePaginator
    {
        return $this->getPostsQuery($sort)->paginate($perPage);
    }

    public function getPostsBySubreddit(Subreddit $subreddit, string $sort = 'hot', int $perPage = 20): LengthAwarePaginator
    {
        return $this->getPostsQuery($sort)
            ->bySubreddit($subreddit->id)
            ->paginate($perPage);
    }

    public function getPostsByUser(User $user, int $perPage = 20): LengthAwarePaginator
    {
        return $user->posts()
            ->with(['subreddit', 'user'])
            ->withCount('comments')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getPostWithComments(int $postId): ?Post
    {
        return Post::with([
            'user',
            'subreddit',
            'comments' => function ($query): void {
                $query->root()
                    ->with(['user', 'children.user'])
                    ->withCount('children')
                    ->orderBy('created_at', 'asc');
            },
        ])->find($postId);
    }

    public function parseMarkdown(string $content): string
    {
        // Basic markdown parsing - can be enhanced with a proper library later
        $html = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

        // Headers
        $html = preg_replace('/^### (.*)$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^## (.*)$/m', '<h2>$1</h2>', (string) $html);
        $html = preg_replace('/^# (.*)$/m', '<h1>$1</h1>', (string) $html);

        // Bold
        $html = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', (string) $html);

        // Italic
        $html = preg_replace('/\*(.*?)\*/', '<em>$1</em>', (string) $html);

        // Links
        $html = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank" rel="noopener">$1</a>', (string) $html);

        // Code blocks
        $html = preg_replace('/```([^`]+)```/', '<pre><code>$1</code></pre>', (string) $html);

        // Inline code
        $html = preg_replace('/`([^`]+)`/', '<code>$1</code>', (string) $html);

        // Line breaks
        $html = nl2br((string) $html);

        return $html;
    }

    public function searchPosts(string $query, ?Subreddit $subreddit = null, int $perPage = 20): LengthAwarePaginator
    {
        $postsQuery = Post::with(['user', 'subreddit'])
            ->withCount('comments')
            ->where(function ($q) use ($query): void {
                $q->where('title', 'like', sprintf('%%%s%%', $query))
                    ->orWhere('content', 'like', sprintf('%%%s%%', $query));
            });

        if ($subreddit instanceof Subreddit) {
            $postsQuery->bySubreddit($subreddit->id);
        }

        return $postsQuery->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getTrendingPosts(int $limit = 10): Collection
    {
        return Post::with(['user', 'subreddit'])
            ->withCount(['comments', 'votes'])
            ->where('created_at', '>=', now()->subDay())
            ->orderByRaw('(comments_count + votes_count) desc')
            ->limit($limit)
            ->get();
    }

    public function getPostStats(Post $post): array
    {
        return [
            'id' => $post->id,
            'title' => $post->title,
            'score' => $post->score,
            'comments_count' => $post->comments()->count(),
            'upvotes_count' => $post->upvote_count,
            'downvotes_count' => $post->downvote_count,
            'created_at' => $post->created_at,
            'time_ago' => $post->time_ago,
            'path' => $post->path,
        ];
    }

    public function updatePostScore(Post $post): void
    {
        $post->updateScore();
    }

    private function getPostsQuery(string $sort)
    {
        $query = Post::with(['user', 'subreddit'])->withCount('comments');

        match ($sort) {
            'new' => $query->new(),
            'top' => $query->top(),
            default => $query->hot(),
        };

        return $query;
    }
}
