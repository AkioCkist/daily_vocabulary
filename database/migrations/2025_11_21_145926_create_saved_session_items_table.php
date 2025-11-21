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
        Schema::create('saved_session_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saved_session_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('flashcard_id'); // We'll add foreign key constraint later if needed
            $table->integer('position'); // Order of appearance in the session
            $table->timestamps();
            
            // Unique constraint: one flashcard can only appear once per session at specific position
            $table->unique(['saved_session_id', 'flashcard_id']);
            $table->unique(['saved_session_id', 'position']);
            
            // Index for better performance
            $table->index(['saved_session_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_session_items');
    }
};
