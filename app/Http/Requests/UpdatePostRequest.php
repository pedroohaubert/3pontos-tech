<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTOs\PostDTO;
use Illuminate\Foundation\Http\FormRequest;

final class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        $post = $this->route('post');

        return auth()->check()
               && (auth()->id() === $post->user_id || auth()->user()->isAdmin());
    }

    public function rules(): array
    {
        return [
            'title' => [
                'sometimes',
                'required',
                'string',
                'min:5',
                'max:200',
            ],
            'content' => [
                'sometimes',
                'required',
                'string',
                'min:10',
                'max:40000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Post title is required.',
            'title.min' => 'Post title must be at least 5 characters.',
            'title.max' => 'Post title cannot exceed 200 characters.',
            'content.required' => 'Post content is required.',
            'content.min' => 'Post content must be at least 10 characters.',
            'content.max' => 'Post content cannot exceed 40,000 characters.',
        ];
    }

    public function toDTO(): PostDTO
    {
        $post = $this->route('post');

        return PostDTO::fromArray([
            'title' => $this->validated()['title'] ?? $post->title,
            'content' => $this->validated()['content'] ?? $post->content,
            'user_id' => $post->user_id,
            'subreddit_id' => $post->subreddit_id,
            'id' => $post->id,
            'score' => $post->score,
        ]);
    }
}
