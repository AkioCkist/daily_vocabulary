<?php

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
        Schema::create('flashcard_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('word_id')->constrained()->onDelete('cascade');
            $table->string('mode'); // 'standard' or 'fill_blank'
            $table->boolean('is_correct');
            $table->boolean('was_forgotten')->default(false);
            $table->integer('hints_used')->default(0);
            $table->integer('response_time_ms')->nullable();
            $table->text('user_answer')->nullable(); // For fill-in-the-blank mode
            $table->json('hint_progression')->nullable(); // Track which characters were revealed
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'word_id']);
            $table->index(['user_id', 'mode']);
            $table->index(['user_id', 'was_forgotten']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flashcard_attempts');
    }
};