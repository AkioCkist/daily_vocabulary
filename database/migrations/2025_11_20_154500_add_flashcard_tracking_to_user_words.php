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
        Schema::table('user_words', function (Blueprint $table) {
            // Flashcard tracking
            $table->integer('forgotten_count')->default(0)->after('mistake_count');
            $table->integer('hint_reveals_used')->default(0)->after('forgotten_count');
            $table->integer('fill_blank_attempts')->default(0)->after('hint_reveals_used');
            $table->integer('standard_flashcard_attempts')->default(0)->after('fill_blank_attempts');
            $table->timestamp('last_forgotten_at')->nullable()->after('standard_flashcard_attempts');
            
            // Learning effectiveness tracking
            $table->decimal('difficulty_score', 3, 2)->default(0.50)->after('last_forgotten_at'); // 0.0 = easy, 1.0 = very hard
            $table->integer('consecutive_correct_fill_blank')->default(0)->after('difficulty_score');
            $table->integer('consecutive_correct_standard')->default(0)->after('consecutive_correct_fill_blank');
            
            // Add indexes for performance
            $table->index(['user_id', 'forgotten_count']);
            $table->index(['user_id', 'difficulty_score']);
            $table->index('last_forgotten_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_words', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'forgotten_count']);
            $table->dropIndex(['user_id', 'difficulty_score']);
            $table->dropIndex(['last_forgotten_at']);
            
            $table->dropColumn([
                'forgotten_count',
                'hint_reveals_used',
                'fill_blank_attempts',
                'standard_flashcard_attempts',
                'last_forgotten_at',
                'difficulty_score',
                'consecutive_correct_fill_blank',
                'consecutive_correct_standard'
            ]);
        });
    }
};