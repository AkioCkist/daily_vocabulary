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
            // Add new columns for enhanced functionality
            $table->boolean('is_learned')->default(false)->after('word_id');
            $table->boolean('mastered')->default(false)->after('is_learned');
            $table->integer('consecutive_correct')->default(0)->after('mastered');
            $table->integer('mistake_count')->default(0)->after('consecutive_correct');
            $table->timestamp('next_review_at')->nullable()->after('mistake_count');
            $table->timestamp('last_seen_at')->nullable()->after('next_review_at');
            
            // Add indexes for performance
            $table->index(['user_id', 'is_learned']);
            $table->index(['user_id', 'mastered']);
            $table->index(['user_id', 'mistake_count']);
            $table->index('next_review_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_words', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_learned']);
            $table->dropIndex(['user_id', 'mastered']);
            $table->dropIndex(['user_id', 'mistake_count']);
            $table->dropIndex(['next_review_at']);
            
            $table->dropColumn([
                'is_learned',
                'mastered',
                'consecutive_correct',
                'mistake_count',
                'next_review_at',
                'last_seen_at'
            ]);
        });
    }
};
