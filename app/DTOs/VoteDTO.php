<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\Vote;

final readonly class VoteDTO
{
    public const string TYPE_UP = 'up';

    public const string TYPE_DOWN = 'down';

    public function __construct(
        public int $userId,
        public string $voteableType,
        public int $voteableId,
        public string $type,
        public ?int $id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            voteableType: $data['voteable_type'],
            voteableId: $data['voteable_id'],
            type: $data['type'],
            id: $data['id'] ?? null,
        );
    }

    public static function fromModel(Vote $vote): self
    {
        return new self(
            userId: $vote->user_id,
            voteableType: $vote->voteable_type,
            voteableId: $vote->voteable_id,
            type: $vote->type,
            id: $vote->id,
        );
    }

    public static function getAllowedTypes(): array
    {
        return [self::TYPE_UP, self::TYPE_DOWN];
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'voteable_type' => $this->voteableType,
            'voteable_id' => $this->voteableId,
            'type' => $this->type,
        ];
    }

    public function isUpvote(): bool
    {
        return $this->type === self::TYPE_UP;
    }

    public function isDownvote(): bool
    {
        return $this->type === self::TYPE_DOWN;
    }

    public function getScoreChange(): int
    {
        return $this->isUpvote() ? 1 : -1;
    }
}
