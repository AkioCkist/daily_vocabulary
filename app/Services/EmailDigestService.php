<?php

namespace App\Services;

use App\Jobs\SendIncorrectWordsDigestJob;
use App\Jobs\SendTopicSummaryDigestJob;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmailDigestService
{
    public function shouldSend(Subscription $sub, string $type): bool
    {
        $now = now();
        $last = match ($type) {
            'incorrect_words' => $sub->last_incorrect_words_sent_at,
            'topic_summary' => $sub->last_topic_summary_sent_at,
            default => null,
        };

        $freq = match ($type) {
            'incorrect_words' => $sub->incorrect_words_frequency,
            'topic_summary' => $sub->topic_summary_frequency,
            default => 'none',
        };

        if ($freq === 'none') return false;

        $intervalDays = $freq === 'weekly' ? 7 : 30;

        // Use confirmed_at as baseline for first send to avoid immediate digest
        $baseline = $last ?? $sub->confirmed_at ?? $sub->created_at;
        if (!$baseline) {
            // If no timestamps found, be conservative: wait one interval
            return false;
        }

        return $baseline->diffInDays($now) >= $intervalDays;
    }

    /**
     * OPTIMIZED: Dispatches incorrect words digest email to queue instead of sending synchronously.
     * This prevents blocking the HTTP request while waiting for email delivery.
     */
    public function sendIncorrectWordsReport(User $user): void
    {
        $subject = 'Your Frequently Incorrect Words';
        
        // Dispatch job to queue asynchronously
        SendIncorrectWordsDigestJob::dispatch($user)
            ->onQueue('default')
            ->delay(now()->addSeconds(5)); // Small delay to batch jobs
        
        $this->logEmail($user->id, 'incorrect_words', $subject);

        $user->subscription?->update(['last_incorrect_words_sent_at' => now()]);
        
        Log::info('Incorrect Words digest job queued', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * OPTIMIZED: Dispatches topic summary digest email to queue instead of sending synchronously.
     * This prevents blocking the HTTP request while waiting for email delivery.
     */
    public function sendTopicSummary(User $user): void
    {
        $subject = 'Your Learning Topic Summary';
        
        // Dispatch job to queue asynchronously
        SendTopicSummaryDigestJob::dispatch($user)
            ->onQueue('default')
            ->delay(now()->addSeconds(5)); // Small delay to batch jobs
        
        $this->logEmail($user->id, 'topic_summary', $subject);

        $user->subscription?->update(['last_topic_summary_sent_at' => now()]);
        
        Log::info('Topic Summary digest job queued', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    public function logEmail(int $userId, string $type, string $subject, array $meta = []): void
    {
        DB::table('email_logs')->insert([
            'user_id' => $userId,
            'type' => $type,
            'subject' => $subject,
            'meta' => $meta ? json_encode($meta) : null,
            'sent_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
