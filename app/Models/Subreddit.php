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

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'user_id',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Post, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    protected function scopeActive($query)
    {
        return $query->whereHas('posts');
    }

    protected function scopePopular($query)
    {
        return $query->withCount('posts')
            ->orderBy('posts_count', 'desc');
    }

    protected function getPostCountAttribute(): int
    {
        return $this->posts()->count();
    }

    protected function getPathAttribute(): string
    {
        return '/r/'.$this->name;
    }

    protected function getPrefixedNameAttribute(): string
    {
        return 'r/'.$this->name;
    }
}
