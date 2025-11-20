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
        Schema::table('topics', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('description');
            $table->foreignId('user_id')->nullable()->after('is_system')->constrained()->onDelete('cascade');
            
            // Add index for better performance
            $table->index(['is_system', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropIndex(['is_system', 'user_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['is_system', 'user_id']);
        });
    }
};
