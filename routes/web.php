<?php

declare(strict_types=1);

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubredditController;
use App\Http\Controllers\VoteController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

// Rotas públicas (não logadas)
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/r/{subreddit}', [SubredditController::class, 'show'])->name('subreddits.show');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// Dashboard para usuários logados
Route::get('/dashboard', fn (): View|Factory => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');

// Grupo de rotas protegidas (requer autenticação)
Route::middleware('auth')->group(function (): void {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Subreddits
    Route::get('/subreddits', [SubredditController::class, 'index'])->name('subreddits.index');
    Route::get('/subreddits/create', [SubredditController::class, 'create'])->name('subreddits.create');
    Route::post('/subreddits', [SubredditController::class, 'store'])->name('subreddits.store');
    Route::get('/subreddits/{subreddit}/edit', [SubredditController::class, 'edit'])->name('subreddits.edit');
    Route::put('/subreddits/{subreddit}', [SubredditController::class, 'update'])->name('subreddits.update');
    Route::delete('/subreddits/{subreddit}', [SubredditController::class, 'destroy'])->name('subreddits.destroy');

    // Posts
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Comments
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/comments/{comment}/edit', [CommentController::class, 'edit'])->name('comments.edit');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Votes
    Route::post('/votes', [VoteController::class, 'store'])->name('votes.store');
    Route::delete('/votes/{vote}', [VoteController::class, 'destroy'])->name('votes.destroy');
});

require __DIR__.'/auth.php';
