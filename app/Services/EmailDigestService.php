<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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

    public function sendIncorrectWordsReport(User $user): void
    {
        $subject = 'Your Frequently Incorrect Words';
        try {
            Mail::to($user->email)->send(new \App\Mail\IncorrectWordsDigestMail($user));
        } catch (\Throwable $e) {
            Log::warning('Failed to send Incorrect Words digest: '.$e->getMessage());
        }
        $this->logEmail($user->id, 'incorrect_words', $subject);

        $user->subscription?->update(['last_incorrect_words_sent_at' => now()]);
    }

    public function sendTopicSummary(User $user): void
    {
        $subject = 'Your Learning Topic Summary';
        try {
            Mail::to($user->email)->send(new \App\Mail\TopicSummaryDigestMail($user));
        } catch (\Throwable $e) {
            Log::warning('Failed to send Topic Summary digest: '.$e->getMessage());
        }
        $this->logEmail($user->id, 'topic_summary', $subject);

        $user->subscription?->update(['last_topic_summary_sent_at' => now()]);
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
