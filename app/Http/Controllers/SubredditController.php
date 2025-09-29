<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubredditRequest;
use App\Http\Requests\UpdateSubredditRequest;
use App\Models\Subreddit;
use App\Services\SubredditService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class SubredditController extends Controller
{
    public function __construct(
        private readonly SubredditService $subredditService,
    ) {}

    public function index(): View|Factory
    {
        $subreddits = $this->subredditService->getAllSubreddits();

        return view('subreddits.index', ['subreddits' => $subreddits]);
    }

    public function show(Subreddit $subreddit): View|Factory
    {
        return view('subreddits.show', ['subreddit' => $subreddit]);
    }

    public function create(): View|Factory
    {
        return view('subreddits.create');
    }

    public function store(StoreSubredditRequest $request): RedirectResponse
    {
        $subreddit = $this->subredditService->createSubreddit($request->toDTO());

        return redirect()
            ->route('subreddits.show', $subreddit)
            ->with('success', 'Subreddit created successfully.');
    }

    public function edit(Subreddit $subreddit): View|Factory
    {
        return view('subreddits.edit', ['subreddit' => $subreddit]);
    }

    public function update(UpdateSubredditRequest $request, Subreddit $subreddit): RedirectResponse
    {
        $updatedSubreddit = $this->subredditService->updateSubreddit($subreddit, $request->toDTO());

        return redirect()
            ->route('subreddits.show', $updatedSubreddit)
            ->with('success', 'Subreddit updated successfully.');
    }

    public function destroy(Subreddit $subreddit): RedirectResponse
    {
        $this->subredditService->deleteSubreddit($subreddit);

        return redirect()
            ->route('subreddits.index')
            ->with('success', 'Subreddit deleted successfully.');
    }
}
