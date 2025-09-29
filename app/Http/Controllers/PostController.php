<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService,
    ) {}

    public function index(): View|Factory
    {
        $posts = $this->postService->getAllPosts();

        return view('posts.index', ['posts' => $posts]);
    }

    public function show(Post $post): View|Factory
    {
        return view('posts.show', ['post' => $post]);
    }

    public function create(): View|Factory
    {
        return view('posts.create');
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $post = $this->postService->createPost($request->toDTO());

        return redirect()
            ->route('posts.show', $post)
            ->with('success', 'Post created successfully.');
    }

    public function edit(Post $post): View|Factory
    {
        return view('posts.edit', ['post' => $post]);
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $updatedPost = $this->postService->updatePost($post, $request->toDTO());

        return redirect()
            ->route('posts.show', $updatedPost)
            ->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->postService->deletePost($post);

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post deleted successfully.');
    }
}
