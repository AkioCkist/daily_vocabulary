<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\TestService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to generate daily tests for users.
 */
class GenerateDailyTestsJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 300;

    /**
     * Create a new job instance.
     *
     * @param int|null $userId Generate test for specific user, or all users if null
     * @param array<string, mixed> $config Test configuration
     */
    public function __construct(
        private ?int $userId = null,
        private array $config = []
    ) {}

    /**
     * Execute the job.
     */
    public function handle(TestService $testService): void
    {
        try {
            if ($this->userId) {
                // Generate test for specific user
                $user = User::findOrFail($this->userId);
                $this->generateTestForUser($testService, $user);
            } else {
                // Generate tests for all active users
                $this->generateTestsForAllUsers($testService);
            }
        } catch (\Exception $e) {
            Log::error('Failed to generate daily tests', [
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Generate test for a specific user.
     */
    private function generateTestForUser(TestService $testService, User $user): void
    {
        $test = $testService->generateDailyTest($user, $this->config);
        
        Log::info('Generated daily test', [
            'user_id' => $user->id,
            'test_id' => $test->id,
            'items_count' => $test->items()->count(),
        ]);
    }

    /**
     * Generate tests for all active users.
     */
    private function generateTestsForAllUsers(TestService $testService): void
    {
        $activeUsers = User::whereHas('userWords')
            ->orWhereHas('dailyTests', function ($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            })
            ->get();

        $generated = 0;
        $errors = 0;

        foreach ($activeUsers as $user) {
            try {
                $this->generateTestForUser($testService, $user);
                $generated++;
            } catch (\Exception $e) {
                $errors++;
                Log::warning('Failed to generate test for user', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Completed daily test generation', [
            'total_users' => $activeUsers->count(),
            'generated' => $generated,
            'errors' => $errors,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateDailyTestsJob failed', [
            'user_id' => $this->userId,
            'exception' => $exception->getMessage(),
        ]);
    }
}
