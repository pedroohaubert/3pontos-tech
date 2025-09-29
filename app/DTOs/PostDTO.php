<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\Post;
use LogicException;

final readonly class PostDTO
{
    public function __construct(
        public string $title,
        public string $content,
        public int $userId,
        public int $subredditId,
        public ?int $id = null,
        public int $score = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            content: $data['content'],
            userId: $data['user_id'],
            subredditId: $data['subreddit_id'],
            id: $data['id'] ?? null,
            score: $data['score'] ?? 0,
        );
    }

    public static function fromModel(Post $post): self
    {
        return new self(
            title: $post->title,
            content: $post->content,
            userId: $post->user_id,
            subredditId: $post->subreddit_id,
            id: $post->id,
            score: $post->score,
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'user_id' => $this->userId,
            'subreddit_id' => $this->subredditId,
            'score' => $this->score,
        ];
    }

    public function getPath(): string
    {
        throw_if($this->id === null, new LogicException('Cannot get path for post without ID'));

        return sprintf('/r/%s/%d', $this->getSubredditName(), $this->id);
    }

    private function getSubredditName(): string
    {
        // This is a placeholder - in real implementation, you'd inject a repository
        // or service to get the subreddit name
        return 'subreddit';
    }
}
