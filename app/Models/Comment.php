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

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'content',
        'user_id',
        'post_id',
        'parent_id',
        'score',
        'depth',
    ];

    /**
     * Get the user that created this comment.
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the post this comment belongs to.
     * @return BelongsTo<Post, $this>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the parent comment (for nested replies).
     * @return BelongsTo<\App\Models\Comment, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get all child comments (replies to this comment).
     * @return HasMany<\App\Models\Comment, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Get all votes on this comment.
     * @return MorphMany<Vote, $this>
     */
    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'voteable');
    }

    /**
     * Scope to get only root comments (no parent).
     */
    protected function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get comments by post.
     */
    protected function scopeByPost($query, $postId)
    {
        return $query->where('post_id', $postId);
    }

    /**
     * Scope to get comments by user.
     */
    protected function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get comments by depth level.
     */
    protected function scopeByDepth($query, $depth)
    {
        return $query->where('depth', $depth);
    }

    /**
     * Check if this is a reply to another comment.
     */
    public function isReply(): bool
    {
        return $this->parent_id !== null;
    }

    /**
     * Get the entire thread path from root to this comment.
     */
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

    /**
     * Get the time since creation in human readable format.
     */
    protected function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the total number of replies to this comment.
     */
    protected function getReplyCountAttribute(): int
    {
        return $this->children()->count();
    }

    /**
     * Get the upvote count for this comment.
     */
    protected function getUpvoteCountAttribute(): int
    {
        return $this->votes()->where('type', 'up')->count();
    }

    /**
     * Get the downvote count for this comment.
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
     * Update depth when creating a reply.
     */
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

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'depth' => 'integer',
        ];
    }
}
