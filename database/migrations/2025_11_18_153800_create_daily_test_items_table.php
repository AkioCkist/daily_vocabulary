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
        Schema::create('daily_test_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_test_id')->constrained()->onDelete('cascade');
            $table->foreignId('word_id')->constrained()->onDelete('cascade');
            $table->enum('question_type', ['word_to_definition', 'definition_to_word', 'word_to_meaning', 'meaning_to_word']);
            $table->json('options')->nullable();
            $table->text('correct_answer');
            $table->json('result')->nullable();
            $table->timestamps();
            
            $table->index(['daily_test_id', 'word_id']);
            $table->index('question_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_test_items');
    }
};
