<?php

namespace App\Console\Commands;

use App\Jobs\GenerateDailyTestsJob;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Command to generate daily tests for all users.
 */
class GenerateDailyTests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vocabulary:generate-daily-tests 
                           {--user= : Generate test for specific user ID}
                           {--queue : Queue the job instead of running synchronously}
                           {--test-length=20 : Number of questions in the test}
                           {--new-ratio=0.4 : Ratio of new words}
                           {--review-ratio=0.4 : Ratio of review words}
                           {--unmastered-ratio=0.2 : Ratio of unmastered words}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily vocabulary tests for users';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting daily test generation...');
        
        $userId = $this->option('user');
        $useQueue = $this->option('queue');
        
        $config = [
            'test_length' => (int) $this->option('test-length'),
            'new_words_ratio' => (float) $this->option('new-ratio'),
            'review_words_ratio' => (float) $this->option('review-ratio'),
            'unmastered_words_ratio' => (float) $this->option('unmastered-ratio'),
        ];

        // Validate ratios sum to 1
        $ratioSum = $config['new_words_ratio'] + $config['review_words_ratio'] + $config['unmastered_words_ratio'];
        if (abs($ratioSum - 1.0) > 0.01) {
            $this->error('The sum of ratios must equal 1.0. Current sum: ' . $ratioSum);
            return self::FAILURE;
        }

        try {
            if ($userId) {
                $this->generateForUser((int) $userId, $config, $useQueue);
            } else {
                $this->generateForAllUsers($config, $useQueue);
            }

            $this->info('Daily test generation completed successfully!');
            return self::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Failed to generate daily tests: ' . $e->getMessage());
            Log::error('Daily test generation command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return self::FAILURE;
        }
    }

    /**
     * Generate test for a specific user.
     *
     * @param int $userId
     * @param array<string, mixed> $config
     * @param bool $useQueue
     */
    private function generateForUser(int $userId, array $config, bool $useQueue): void
    {
        $user = User::findOrFail($userId);
        $this->info("Generating daily test for user: {$user->name} (ID: {$userId})");

        if ($useQueue) {
            GenerateDailyTestsJob::dispatch($userId, $config);
            $this->info('Test generation job queued for user.');
        } else {
            $job = new GenerateDailyTestsJob($userId, $config);
            $job->handle(app(\App\Services\TestService::class));
            $this->info('Test generated successfully for user.');
        }
    }

    /**
     * Generate tests for all active users.
     *
     * @param array<string, mixed> $config
     * @param bool $useQueue
     */
    private function generateForAllUsers(array $config, bool $useQueue): void
    {
        $activeUserCount = User::whereHas('userWords')
            ->orWhereHas('dailyTests', function ($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            })
            ->count();

        $this->info("Generating daily tests for {$activeUserCount} active users...");

        if ($useQueue) {
            GenerateDailyTestsJob::dispatch(null, $config);
            $this->info('Bulk test generation job queued.');
        } else {
            $progressBar = $this->output->createProgressBar($activeUserCount);
            $progressBar->start();

            $job = new GenerateDailyTestsJob(null, $config);
            $job->handle(app(\App\Services\TestService::class));
            
            $progressBar->finish();
            $this->newLine();
            $this->info('Tests generated successfully for all active users.');
        }
    }
}
