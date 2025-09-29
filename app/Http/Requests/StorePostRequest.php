<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTOs\PostDTO;
use App\Models\Subreddit;
use Illuminate\Foundation\Http\FormRequest;

final class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'min:5',
                'max:200',
            ],
            'content' => [
                'required',
                'string',
                'min:10',
                'max:40000',
            ],
            'subreddit_id' => [
                'required',
                'integer',
                'exists:subreddits,id',
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
            'subreddit_id.required' => 'Subreddit is required.',
            'subreddit_id.exists' => 'Selected subreddit does not exist.',
        ];
    }

    public function toDTO(): PostDTO
    {
        return PostDTO::fromArray([
            'title' => $this->validated()['title'],
            'content' => $this->validated()['content'],
            'user_id' => auth()->id(),
            'subreddit_id' => $this->validated()['subreddit_id'],
        ]);
    }

    protected function prepareForValidation(): void
    {
        // If subreddit name is provided instead of ID, find the ID
        if ($this->has('subreddit') && ! $this->has('subreddit_id')) {
            $subreddit = Subreddit::query()->where('name', $this->input('subreddit'))->first();
            if ($subreddit) {
                $this->merge(['subreddit_id' => $subreddit->id]);
            }
        }
    }
}
