<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\VoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Vote extends Model
{
    /** @use HasFactory<VoteFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'voteable_type',
        'voteable_id',
        'type',
    ];

    public static function toggle(User $user, $voteable, string $type): ?self
    {
        $existingVote = self::query()->where('user_id', $user->id)
            ->where('voteable_type', $voteable::class)
            ->where('voteable_id', $voteable->id)
            ->first();

        if ($existingVote) {
            if ($existingVote->type === $type) {
                $existingVote->delete();

                return null;
            }

            $existingVote->update(['type' => $type]);

            return $existingVote;

        }

        return self::query()->create([
            'user_id' => $user->id,
            'voteable_type' => $voteable::class,
            'voteable_id' => $voteable->id,
            'type' => $type,
        ]);
    }

    public static function removeVote(User $user, $voteable): bool
    {
        return self::query()->where('user_id', $user->id)
            ->where('voteable_type', $voteable::class)
            ->where('voteable_id', $voteable->id)
            ->delete() > 0;
    }

    public static function hasVoted(User $user, $voteable, ?string $type = null): bool
    {
        $query = self::query()->where('user_id', $user->id)
            ->where('voteable_type', $voteable::class)
            ->where('voteable_id', $voteable->id);

        if ($type !== null && $type !== '' && $type !== '0') {
            $query->where('type', $type);
        }

        return $query->exists();
    }

    public static function getUserVote(User $user, $voteable): ?string
    {
        $vote = self::query()->where('user_id', $user->id)
            ->where('voteable_type', $voteable::class)
            ->where('voteable_id', $voteable->id)
            ->first();

        return $vote?->type;
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voteable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted(): void
    {
        self::created(function (Vote $vote): void {
            $vote->voteable->updateScore();
        });

        self::updated(function (Vote $vote): void {
            $vote->voteable->updateScore();
        });

        self::deleted(function (Vote $vote): void {
            $vote->voteable->updateScore();
        });
    }

    protected function scopeUpvotes($query)
    {
        return $query->where('type', 'up');
    }

    protected function scopeDownvotes($query)
    {
        return $query->where('type', 'down');
    }

    protected function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    protected function scopeForType($query, string $type)
    {
        return $query->where('voteable_type', $type);
    }

    protected function casts(): array
    {
        return [
            'type' => 'string',
        ];
    }
}
