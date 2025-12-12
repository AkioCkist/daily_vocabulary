<?php

namespace App\Jobs;

use App\Mail\IncorrectWordsDigestMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * OPTIMIZED: Sends incorrect words digest email asynchronously via queue.
 * Prevents blocking HTTP requests while waiting for email delivery.
 */
class SendIncorrectWordsDigestJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private User $user
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->user->email)->send(new IncorrectWordsDigestMail($this->user));
            
            Log::info('Incorrect Words digest sent successfully', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send Incorrect Words digest', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
                'error' => $e->getMessage(),
            ]);
            
            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Handle a failed job.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Incorrect Words digest job failed permanently', [
            'user_id' => $this->user->id,
            'email' => $this->user->email,
            'error' => $exception->getMessage(),
        ]);
    }
}
