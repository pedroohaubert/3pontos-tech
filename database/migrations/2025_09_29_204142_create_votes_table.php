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
        Schema::create('votes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Voter
            $table->morphs('voteable'); // Creates voteable_type and voteable_id columns for polymorphic relationship
            $table->enum('type', ['up', 'down']); // Vote type: upvote or downvote
            $table->timestamps();

            // Unique constraint: one vote per user per item (prevents double voting)
            $table->unique(['user_id', 'voteable_type', 'voteable_id']);

            // Additional index for performance (morphs() already creates voteable_type_voteable_id_index)
            $table->index(['user_id', 'created_at']); // For user's voting history
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
