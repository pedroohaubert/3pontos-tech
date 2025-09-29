<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SubredditFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Subreddit extends Model
{
    /** @use HasFactory<SubredditFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'user_id',
    ];

    /**
     * Get the user that created this subreddit.
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all posts in this subreddit.
     * @return HasMany<Post, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Scope to get active subreddits (those with posts).
     */
    protected function scopeActive($query)
    {
        return $query->whereHas('posts');
    }

    /**
     * Scope to get popular subreddits (by post count).
     */
    protected function scopePopular($query)
    {
        return $query->withCount('posts')
            ->orderBy('posts_count', 'desc');
    }

    /**
     * Get the total number of posts in this subreddit.
     */
    protected function getPostCountAttribute(): int
    {
        return $this->posts()->count();
    }

    /**
     * Get the URL path for this subreddit.
     */
    protected function getPathAttribute(): string
    {
        return '/r/' . $this->name;
    }

    /**
     * Get the display name with r/ prefix.
     */
    protected function getPrefixedNameAttribute(): string
    {
        return 'r/' . $this->name;
    }
}
