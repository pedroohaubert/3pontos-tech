<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Models\User;

final readonly class UserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password = null,
        public string $role = 'user',
        public ?int $id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'] ?? null,
            role: $data['role'] ?? 'user',
            id: $data['id'] ?? null,
        );
    }

    public static function fromModel(User $user): self
    {
        return new self(
            name: $user->name,
            email: $user->email,
            role: $user->role,
            id: $user->id,
        );
    }

    public function toArray(): array
    {
        $array = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if ($this->password !== null) {
            $array['password'] = $this->password;
        }

        return $array;
    }
}
