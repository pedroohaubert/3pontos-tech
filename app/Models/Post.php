<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'content',
        'user_id',
        'subreddit_id',
        'score',
    ];

    /**
     * Get the user that created this post.
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subreddit this post belongs to.
     * @return BelongsTo<Subreddit, $this>
     */
    public function subreddit(): BelongsTo
    {
        return $this->belongsTo(Subreddit::class);
    }

    /**
     * Get all comments on this post.
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get all votes on this post.
     * @return MorphMany<Vote, $this>
     */
    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'voteable');
    }

    /**
     * Scope to get posts by subreddit.
     */
    protected function scopeBySubreddit($query, $subredditId)
    {
        return $query->where('subreddit_id', $subredditId);
    }

    /**
     * Scope to order posts by newest first.
     */
    protected function scopeNew($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope to order posts by score (top posts).
     */
    protected function scopeTop($query)
    {
        return $query->orderBy('score', 'desc');
    }

    /**
     * Scope to order posts by "hot" algorithm (Reddit-style).
     * Simplified version: score with time decay.
     */
    protected function scopeHot($query)
    {
        return $query->orderByRaw('(score - 1) / POW(TIMESTAMPDIFF(HOUR, created_at, NOW()) + 2, 1.8) DESC');
    }

    /**
     * Get the total number of comments on this post.
     */
    protected function getCommentCountAttribute(): int
    {
        return $this->comments()->count();
    }

    /**
     * Get the time since creation in human readable format.
     */
    protected function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the URL path for this post.
     */
    protected function getPathAttribute(): string
    {
        return sprintf('/r/%s/%d', $this->subreddit->name, $this->id);
    }

    /**
     * Get the upvote count for this post.
     */
    protected function getUpvoteCountAttribute(): int
    {
        return $this->votes()->where('type', 'up')->count();
    }

    /**
     * Get the downvote count for this post.
     */
    protected function getDownvoteCountAttribute(): int
    {
        return $this->votes()->where('type', 'down')->count();
    }

    /**
     * Update the score based on current votes.
     */
    public function updateScore(): void
    {
        $upvotes = $this->votes()->where('type', 'up')->count();
        $downvotes = $this->votes()->where('type', 'down')->count();
        $this->update(['score' => $upvotes - $downvotes]);
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'score' => 'integer',
        ];
    }
}
