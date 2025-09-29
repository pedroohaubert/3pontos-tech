<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTOs\SubredditDTO;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateSubredditRequest extends FormRequest
{
    public function authorize(): bool
    {
        $subreddit = $this->route('subreddit');

        return auth()->check()
               && (auth()->id() === $subreddit->user_id || auth()->user()->isAdmin());
    }

    public function rules(): array
    {
        $subreddit = $this->route('subreddit');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[a-zA-Z0-9_]+$/',
                'unique:subreddits,name,'.$subreddit->id,
            ],
            'display_name' => [
                'sometimes',
                'required',
                'string',
                'min:3',
                'max:50',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Subreddit name is required.',
            'name.min' => 'Subreddit name must be at least 3 characters.',
            'name.max' => 'Subreddit name cannot exceed 20 characters.',
            'name.regex' => 'Subreddit name can only contain letters, numbers, and underscores.',
            'name.unique' => 'This subreddit name is already taken.',
            'display_name.required' => 'Display name is required.',
            'display_name.min' => 'Display name must be at least 3 characters.',
            'display_name.max' => 'Display name cannot exceed 50 characters.',
            'description.max' => 'Description cannot exceed 500 characters.',
        ];
    }

    public function toDTO(): SubredditDTO
    {
        $subreddit = $this->route('subreddit');

        return SubredditDTO::fromArray([
            'name' => $this->validated()['name'] ?? $subreddit->name,
            'display_name' => $this->validated()['display_name'] ?? $subreddit->display_name,
            'description' => $this->validated()['description'] ?? $subreddit->description,
            'user_id' => $subreddit->user_id,
            'id' => $subreddit->id,
        ]);
    }
}
