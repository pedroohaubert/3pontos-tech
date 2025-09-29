<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTOs\CommentDTO;
use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Foundation\Http\FormRequest;

final class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('create', Comment::class);
    }

    public function rules(): array
    {
        return [
            'content' => [
                'required',
                'string',
                'min:1',
                'max:10000',
            ],
            'post_id' => [
                'required_without:parent_id',
                'integer',
                'exists:posts,id',
            ],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:comments,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Comment content is required.',
            'content.min' => 'Comment cannot be empty.',
            'content.max' => 'Comment cannot exceed 10,000 characters.',
            'post_id.required_without' => 'Post ID is required when not replying to a comment.',
            'post_id.exists' => 'The specified post does not exist.',
            'parent_id.exists' => 'The parent comment does not exist.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($this->filled('parent_id')) {
                $parentComment = Comment::query()->find($this->input('parent_id'));
                if ($parentComment) {
                    $commentService = app(CommentService::class);
                    if (! $commentService->validateCommentDepth($parentComment)) {
                        $validator->errors()->add('parent_id', 'Maximum comment depth exceeded.');
                    }
                }
            }
        });
    }

    public function toDTO(): CommentDTO
    {
        $depth = 0;

        if ($this->filled('parent_id')) {
            $parentComment = Comment::query()->find($this->input('parent_id'));
            $depth = $parentComment ? $parentComment->depth + 1 : 0;
        }

        return CommentDTO::fromArray([
            'content' => $this->validated()['content'],
            'user_id' => auth()->id(),
            'post_id' => $this->validated()['post_id'] ?? $this->getPostIdFromParent(),
            'parent_id' => $this->validated()['parent_id'] ?? null,
            'depth' => $depth,
        ]);
    }

    private function getPostIdFromParent(): ?int
    {
        if ($this->filled('parent_id')) {
            $parentComment = Comment::query()->find($this->input('parent_id'));

            return $parentComment?->post_id;
        }

        return null;
    }
}
