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
        Schema::table('words', function (Blueprint $table) {
            $table->string('topic')->nullable()->after('source');
            $table->enum('cefr_level', ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'])->nullable()->after('topic');
            $table->text('meaning')->nullable()->after('cefr_level');
            
            // Add indexes for better filtering performance
            $table->index('topic');
            $table->index('cefr_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('words', function (Blueprint $table) {
            $table->dropIndex(['topic']);
            $table->dropIndex(['cefr_level']);
            $table->dropColumn(['topic', 'cefr_level', 'meaning']);
        });
    }
};
