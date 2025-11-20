<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DashboardService;
use App\Models\User;

class TestDashboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:dashboard {user_id=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test dashboard service functionality';

    /**
     * Execute the console command.
     */
    public function handle(DashboardService $dashboardService)
    {
        $userId = $this->argument('user_id');
        
        try {
            $user = User::findOrFail($userId);
            $this->info("Testing dashboard for user: {$user->name} (ID: {$user->id})");
            
            $dashboardData = $dashboardService->getDashboardData($user);
            
            $this->info('Dashboard data retrieved successfully!');
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Words Learning', $dashboardData['stats']['words_learning']],
                    ['Words Learned', $dashboardData['stats']['words_learned']],
                    ['Words Mastered', $dashboardData['stats']['words_mastered']],
                    ['Accuracy Rate', $dashboardData['stats']['accuracy_rate'] . '%'],
                    ['Learning Streak', $dashboardData['stats']['learning_streak']],
                    ['System Topics', count($dashboardData['available_topics']['system'])],
                    ['User Topics', count($dashboardData['available_topics']['user'])],
                ]
            );
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error testing dashboard: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
