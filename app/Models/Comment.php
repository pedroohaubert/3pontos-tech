<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'content',
        'user_id',
        'post_id',
        'parent_id',
        'score',
        'depth',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Post, $this>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * @return BelongsTo<\App\Models\Comment, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return HasMany<\App\Models\Comment, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * @return MorphMany<Vote, $this>
     */
    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'voteable');
    }

    public function isReply(): bool
    {
        return $this->parent_id !== null;
    }

    public function getThread(): array
    {
        $thread = [];
        $current = $this;

        while ($current->parent) {
            array_unshift($thread, $current->parent);
            $current = $current->parent;
        }

        return $thread;
    }

    public function updateScore(): void
    {
        $upvotes = $this->votes()->where('type', 'up')->count();
        $downvotes = $this->votes()->where('type', 'down')->count();
        $this->update(['score' => $upvotes - $downvotes]);
    }

    protected static function booted(): void
    {
        self::creating(function (Comment $comment): void {
            if ($comment->parent_id) {
                $parent = self::query()->find($comment->parent_id);
                $comment->depth = $parent ? $parent->depth + 1 : 0;
            } else {
                $comment->depth = 0;
            }
        });
    }

    protected function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    protected function scopeByPost($query, $postId)
    {
        return $query->where('post_id', $postId);
    }

    protected function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    protected function scopeByDepth($query, $depth)
    {
        return $query->where('depth', $depth);
    }

    protected function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    protected function getReplyCountAttribute(): int
    {
        return $this->children()->count();
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
            'depth' => 'integer',
        ];
    }
}
