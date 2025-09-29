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

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'subreddit_id',
        'score',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Subreddit, $this>
     */
    public function subreddit(): BelongsTo
    {
        return $this->belongsTo(Subreddit::class);
    }

    /**
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return MorphMany<Vote, $this>
     */
    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'voteable');
    }

    public function updateScore(): void
    {
        $upvotes = $this->votes()->where('type', 'up')->count();
        $downvotes = $this->votes()->where('type', 'down')->count();
        $this->update(['score' => $upvotes - $downvotes]);
    }

    protected function scopeBySubreddit($query, $subredditId)
    {
        return $query->where('subreddit_id', $subredditId);
    }

    protected function scopeNew($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    protected function scopeTop($query)
    {
        return $query->orderBy('score', 'desc');
    }

    protected function scopeHot($query)
    {
        return $query->orderByRaw('(score - 1) / POW(TIMESTAMPDIFF(HOUR, created_at, NOW()) + 2, 1.8) DESC');
    }

    protected function getCommentCountAttribute(): int
    {
        return $this->comments()->count();
    }

    protected function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    protected function getPathAttribute(): string
    {
        return sprintf('/r/%s/%d', $this->subreddit->name, $this->id);
    }

    protected function getUpvoteCountAttribute(): int
    {
        return $this->votes()->where('type', 'up')->count();
    }

    protected function getDownvoteCountAttribute(): int
    {
        return $this->votes()->where('type', 'down')->count();
    }

    protected function casts(): array
    {
        return [
            'score' => 'integer',
        ];
    }
}
