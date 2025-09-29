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
        Schema::create('subreddits', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique(); // Unique slug like 'technology'
            $table->string('display_name'); // Human readable like 'Technology'
            $table->text('description')->nullable(); // Optional description
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Creator
            $table->timestamps();

            // Index for performance
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subreddits');
    }
};
