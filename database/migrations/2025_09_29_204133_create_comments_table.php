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
        Schema::create('comments', function (Blueprint $table): void {
            $table->id();
            $table->text('content'); // Markdown content, required
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Author
            $table->foreignId('post_id')->constrained()->onDelete('cascade'); // Belongs to post
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade'); // Self-reference for nesting
            $table->integer('score')->default(0); // Calculated vote score
            $table->tinyInteger('depth')->default(0)->unsigned(); // Nesting depth (0-10 max for UI optimization)
            $table->timestamps();
            $table->softDeletes(); // For moderation capabilities

            // Indexes for performance
            $table->index(['post_id', 'score', 'created_at']); // For sorting comments by post
            $table->index(['user_id', 'created_at']); // For user comments
            $table->index(['parent_id']); // For nested comment queries
            $table->index(['depth']); // For depth-based filtering

            // Constraint to limit nesting depth (Reddit-like behavior)
            // Note: This constraint is enforced at application level due to DB limitations
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
