<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTOs\VoteDTO;
use App\Models\Comment;
use App\Models\Post;
use App\Policies\VotePolicy;
use Illuminate\Foundation\Http\FormRequest;

final class VoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! $this->user()) {
            return false;
        }

        $voteableType = $this->input('voteable_type');
        $voteableId = $this->input('voteable_id');

        // Get the voteable model
        $voteable = $voteableType::find($voteableId);

        if (! $voteable) {
            return false;
        }

        $policy = new VotePolicy();

        // Check authorization based on voteable type
        if ($voteable instanceof Post) {
            return $policy->voteOnPost($this->user(), $voteable);
        }

        if ($voteable instanceof Comment) {
            return $policy->voteOnComment($this->user(), $voteable);
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'voteable_type' => [
                'required',
                'string',
                'in:App\\Models\\Post,App\\Models\\Comment',
            ],
            'voteable_id' => [
                'required',
                'integer',
            ],
            'type' => [
                'required',
                'string',
                'in:up,down',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'voteable_type.required' => 'Voteable type is required.',
            'voteable_type.in' => 'Invalid voteable type. Must be Post or Comment.',
            'voteable_id.required' => 'Voteable ID is required.',
            'type.required' => 'Vote type is required.',
            'type.in' => 'Vote type must be either "up" or "down".',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $voteableType = $this->input('voteable_type');
            $voteableId = $this->input('voteable_id');

            if ($voteableType === Post::class) {
                $exists = Post::query()->where('id', $voteableId)->exists();
            } elseif ($voteableType === Comment::class) {
                $exists = Comment::query()->where('id', $voteableId)->exists();
            } else {
                $exists = false;
            }

            if (! $exists) {
                $validator->errors()->add('voteable_id', 'The specified post or comment does not exist.');
            }
        });
    }

    public function toDTO(): VoteDTO
    {
        return VoteDTO::fromArray([
            'user_id' => auth()->id(),
            'voteable_type' => $this->validated()['voteable_type'],
            'voteable_id' => $this->validated()['voteable_id'],
            'type' => $this->validated()['type'],
        ]);
    }
}
