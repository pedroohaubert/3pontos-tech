<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\VoteRequest;
use App\Models\Vote;
use App\Services\VoteService;
use Illuminate\Http\RedirectResponse;

final class VoteController extends Controller
{
    public function __construct(
        private readonly VoteService $voteService,
    ) {}

    public function store(VoteRequest $request): RedirectResponse
    {
        $voteable = $this->getVoteableFromRequest($request);

        if ($request->type === 'up') {
            $this->voteService->upvote(auth()->user(), $voteable);
        } else {
            $this->voteService->downvote(auth()->user(), $voteable);
        }

        return redirect()
            ->back()
            ->with('success', 'Vote recorded successfully.');
    }

    public function destroy(Vote $vote): RedirectResponse
    {
        $this->voteService->removeVote(auth()->user(), $vote->voteable);

        return redirect()
            ->back()
            ->with('success', 'Vote removed successfully.');
    }

    private function getVoteableFromRequest(VoteRequest $request)
    {
        $voteableType = $request->voteable_type;
        $voteableId = $request->voteable_id;

        return $voteableType::findOrFail($voteableId);
    }
}
