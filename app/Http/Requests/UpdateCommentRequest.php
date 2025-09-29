<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTOs\CommentDTO;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $comment = $this->route('comment');

        return auth()->check()
               && (auth()->id() === $comment->user_id || auth()->user()->isAdmin());
    }

    public function rules(): array
    {
        return [
            'content' => [
                'sometimes',
                'required',
                'string',
                'min:1',
                'max:10000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Comment content is required.',
            'content.min' => 'Comment cannot be empty.',
            'content.max' => 'Comment cannot exceed 10,000 characters.',
        ];
    }

    public function toDTO(): CommentDTO
    {
        $comment = $this->route('comment');

        return CommentDTO::fromArray([
            'content' => $this->validated()['content'] ?? $comment->content,
            'user_id' => $comment->user_id,
            'post_id' => $comment->post_id,
            'parent_id' => $comment->parent_id,
            'depth' => $comment->depth,
            'id' => $comment->id,
            'score' => $comment->score,
        ]);
    }
}
