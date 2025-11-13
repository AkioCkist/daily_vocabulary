<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('daily_word_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('word_id')->constrained('words')->cascadeOnDelete();
            $table->date('date')->unique(); // mỗi ngày 1 từ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_word_history');
    }
};
