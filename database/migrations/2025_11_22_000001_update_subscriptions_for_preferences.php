<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'receive_ads')) {
                $table->boolean('receive_ads')->default(false)->after('unsubscribed_at');
            }
            if (!Schema::hasColumn('subscriptions', 'incorrect_words_frequency')) {
                $table->enum('incorrect_words_frequency', ['none','weekly','monthly'])->default('none')->after('receive_ads');
            }
            if (!Schema::hasColumn('subscriptions', 'topic_summary_frequency')) {
                $table->enum('topic_summary_frequency', ['none','weekly','monthly'])->default('none')->after('incorrect_words_frequency');
            }
            if (!Schema::hasColumn('subscriptions', 'last_ads_sent_at')) {
                $table->timestamp('last_ads_sent_at')->nullable()->after('topic_summary_frequency');
            }
            if (!Schema::hasColumn('subscriptions', 'last_incorrect_words_sent_at')) {
                $table->timestamp('last_incorrect_words_sent_at')->nullable()->after('last_ads_sent_at');
            }
            if (!Schema::hasColumn('subscriptions', 'last_topic_summary_sent_at')) {
                $table->timestamp('last_topic_summary_sent_at')->nullable()->after('last_incorrect_words_sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'receive_ads')) {
                $table->dropColumn('receive_ads');
            }
            if (Schema::hasColumn('subscriptions', 'incorrect_words_frequency')) {
                $table->dropColumn('incorrect_words_frequency');
            }
            if (Schema::hasColumn('subscriptions', 'topic_summary_frequency')) {
                $table->dropColumn('topic_summary_frequency');
            }
            if (Schema::hasColumn('subscriptions', 'last_ads_sent_at')) {
                $table->dropColumn('last_ads_sent_at');
            }
            if (Schema::hasColumn('subscriptions', 'last_incorrect_words_sent_at')) {
                $table->dropColumn('last_incorrect_words_sent_at');
            }
            if (Schema::hasColumn('subscriptions', 'last_topic_summary_sent_at')) {
                $table->dropColumn('last_topic_summary_sent_at');
            }
        });
    }
};
