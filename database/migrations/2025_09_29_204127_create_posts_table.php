<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 300); // Required title with max length
            $table->text('content')->nullable(); // Markdown content, nullable for image-only posts
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Author
            $table->foreignId('subreddit_id')->constrained()->onDelete('cascade'); // Belongs to subreddit
            $table->integer('score')->default(0); // Calculated vote score for sorting
            $table->timestamps();
            $table->softDeletes(); // For moderation capabilities

            // Indexes for performance
            $table->index(['subreddit_id', 'score', 'created_at']); // For sorting posts by subreddit
            $table->index(['user_id', 'created_at']); // For user posts
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
