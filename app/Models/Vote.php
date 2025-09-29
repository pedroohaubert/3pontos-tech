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

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'voteable_type',
        'voteable_id',
        'type',
    ];

    /**
     * Toggle between upvote and downvote, or remove vote if same type.
     */
    public static function toggle(User $user, $voteable, string $type): ?self
    {
        // Check if user already voted on this item
        $existingVote = self::query()->where('user_id', $user->id)
            ->where('voteable_type', $voteable::class)
            ->where('voteable_id', $voteable->id)
            ->first();

        if ($existingVote) {
            if ($existingVote->type === $type) {
                // Same vote type - remove the vote
                $existingVote->delete();

                return null;
            }

            // Different vote type - update to new type
            $existingVote->update(['type' => $type]);

            return $existingVote;

        }

        // No existing vote - create new vote
        return self::query()->create([
            'user_id' => $user->id,
            'voteable_type' => $voteable::class,
            'voteable_id' => $voteable->id,
            'type' => $type,
        ]);
    }

    /**
     * Remove a user's vote from a specific item.
     */
    public static function removeVote(User $user, $voteable): bool
    {
        return self::query()->where('user_id', $user->id)
            ->where('voteable_type', $voteable::class)
            ->where('voteable_id', $voteable->id)
            ->delete() > 0;
    }

    /**
     * Check if a user has voted on a specific item.
     */
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

    /**
     * Get the vote type for a specific user and item.
     */
    public static function getUserVote(User $user, $voteable): ?string
    {
        $vote = self::query()->where('user_id', $user->id)
            ->where('voteable_type', $voteable::class)
            ->where('voteable_id', $voteable->id)
            ->first();

        return $vote?->type;
    }

    /**
     * Get the user who cast this vote.
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent voteable model (Post or Comment).
     */
    public function voteable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to get only upvotes.
     */
    protected function scopeUpvotes($query)
    {
        return $query->where('type', 'up');
    }

    /**
     * Scope to get only downvotes.
     */
    protected function scopeDownvotes($query)
    {
        return $query->where('type', 'down');
    }

    /**
     * Scope to get votes by a specific user.
     */
    protected function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get votes for a specific model type.
     */
    protected function scopeForType($query, string $type)
    {
        return $query->where('voteable_type', $type);
    }

    /**
     * Update the voteable model's score after vote changes.
     */
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

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => 'string',
        ];
    }
}
