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
        Schema::create('test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('word_id')->constrained()->onDelete('cascade');
            $table->foreignId('daily_test_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('daily_test_item_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_correct');
            $table->text('answer_text');
            $table->integer('time_taken')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'word_id']);
            $table->index(['daily_test_id', 'is_correct']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_attempts');
    }
};
