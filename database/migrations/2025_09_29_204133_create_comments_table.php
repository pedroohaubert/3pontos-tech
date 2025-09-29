<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table): void {
            $table->id();
            $table->text('content');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
            $table->integer('score')->default(0);
            $table->tinyInteger('depth')->default(0)->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['post_id', 'score', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['parent_id']);
            $table->index(['depth']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
