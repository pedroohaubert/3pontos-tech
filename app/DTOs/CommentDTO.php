<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\Comment;

final readonly class CommentDTO
{
    public function __construct(
        public string $content,
        public int $userId,
        public int $postId,
        public ?int $parentId = null,
        public int $depth = 0,
        public ?int $id = null,
        public int $score = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            content: $data['content'],
            userId: $data['user_id'],
            postId: $data['post_id'],
            parentId: $data['parent_id'] ?? null,
            depth: $data['depth'] ?? 0,
            id: $data['id'] ?? null,
            score: $data['score'] ?? 0,
        );
    }

    public static function fromModel(Comment $comment): self
    {
        return new self(
            content: $comment->content,
            userId: $comment->user_id,
            postId: $comment->post_id,
            parentId: $comment->parent_id,
            depth: $comment->depth,
            id: $comment->id,
            score: $comment->score,
        );
    }

    public function toArray(): array
    {
        return [
            'content' => $this->content,
            'user_id' => $this->userId,
            'post_id' => $this->postId,
            'parent_id' => $this->parentId,
            'depth' => $this->depth,
            'score' => $this->score,
        ];
    }

    public function isReply(): bool
    {
        return $this->parentId !== null;
    }

    public function isRoot(): bool
    {
        return $this->parentId === null;
    }
}
