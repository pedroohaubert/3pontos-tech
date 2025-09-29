<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class CommentController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService,
    ) {}

    public function store(StoreCommentRequest $request): RedirectResponse
    {
        $this->commentService->createComment($request->toDTO());

        return redirect()
            ->back()
            ->with('success', 'Comment created successfully.');
    }

    public function edit(Comment $comment): View|Factory
    {
        return view('comments.edit', ['comment' => $comment]);
    }

    public function update(UpdateCommentRequest $request, Comment $comment): RedirectResponse
    {
        $this->commentService->updateComment($comment, $request->toDTO());

        return redirect()
            ->back()
            ->with('success', 'Comment updated successfully.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $this->commentService->deleteComment($comment);

        return redirect()
            ->back()
            ->with('success', 'Comment deleted successfully.');
    }
}
