<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds composite indexes on frequently queried columns in user_words table
     * to improve query performance and reduce full table scans.
     */
    public function up(): void
    {
        Schema::table('user_words', function (Blueprint $table) {
            // Index for review queries filtering by user_id, mastered status, and mistake count
            $table->index(['user_id', 'mastered', 'mistake_count'], 'idx_user_review_status');
            
            // Index for stats queries filtering by user_id and is_learned status
            $table->index(['user_id', 'is_learned'], 'idx_user_learned');
            
            // Index for ordering queries by last_seen_at
            $table->index(['user_id', 'last_seen_at'], 'idx_user_last_seen');
            
            // Index for progress tracking by consecutive correct answers
            $table->index(['user_id', 'consecutive_correct'], 'idx_user_progress');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_words', function (Blueprint $table) {
            $table->dropIndex('idx_user_review_status');
            $table->dropIndex('idx_user_learned');
            $table->dropIndex('idx_user_last_seen');
            $table->dropIndex('idx_user_progress');
        });
    }
};
