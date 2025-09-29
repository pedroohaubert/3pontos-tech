<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\Subreddit;

final readonly class SubredditDTO
{
    public function __construct(
        public string $name,
        public string $displayName,
        public ?string $description,
        public int $userId,
        public ?int $id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            displayName: $data['display_name'],
            description: $data['description'] ?? null,
            userId: $data['user_id'],
            id: $data['id'] ?? null,
        );
    }

    public static function fromModel(Subreddit $subreddit): self
    {
        return new self(
            name: $subreddit->name,
            displayName: $subreddit->display_name,
            description: $subreddit->description,
            userId: $subreddit->user_id,
            id: $subreddit->id,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'display_name' => $this->displayName,
            'description' => $this->description,
            'user_id' => $this->userId,
        ];
    }

    public function getPrefixedName(): string
    {
        return 'r/'.$this->name;
    }
}
