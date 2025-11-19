<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\UserProgressService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to process user progress updates in the background.
 */
class ProcessUserProgressJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     *
     * @param int $userId
     * @param int $wordId
     * @param bool $isCorrect
     * @param array<string, mixed> $metadata
     */
    public function __construct(
        private int $userId,
        private int $wordId,
        private bool $isCorrect,
        private array $metadata = []
    ) {}

    /**
     * Execute the job.
     */
    public function handle(UserProgressService $userProgressService): void
    {
        try {
            $user = User::findOrFail($this->userId);
            
            // Update word progress
            $userWord = $userProgressService->updateWordProgress(
                $user,
                $this->wordId,
                $this->isCorrect
            );

            // Log progress update
            Log::info('Updated user progress', [
                'user_id' => $this->userId,
                'word_id' => $this->wordId,
                'is_correct' => $this->isCorrect,
                'consecutive_correct' => $userWord->consecutive_correct,
                'mistake_count' => $userWord->mistake_count,
                'mastered' => $userWord->mastered,
                'metadata' => $this->metadata,
            ]);

            // Check for achievements or milestones
            $this->checkForAchievements($user, $userWord);
            
        } catch (\Exception $e) {
            Log::error('Failed to process user progress', [
                'user_id' => $this->userId,
                'word_id' => $this->wordId,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Check for user achievements or milestones.
     */
    private function checkForAchievements(User $user, $userWord): void
    {
        // Check if word was just mastered
        if ($userWord->mastered && $userWord->consecutive_correct === 3) {
            Log::info('User mastered a word', [
                'user_id' => $user->id,
                'word_id' => $this->wordId,
            ]);
            
            // Here you could dispatch notifications, update badges, etc.
        }

        // Check for learning streak milestones
        $stats = app(UserProgressService::class)->getUserProgressStats($user);
        if (in_array($stats['learning_streak'], [7, 30, 100, 365])) {
            Log::info('User reached learning streak milestone', [
                'user_id' => $user->id,
                'streak' => $stats['learning_streak'],
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessUserProgressJob failed', [
            'user_id' => $this->userId,
            'word_id' => $this->wordId,
            'exception' => $exception->getMessage(),
        ]);
    }
}
