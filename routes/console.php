<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily test generation
Schedule::command('vocabulary:generate-daily-tests --queue')
    ->dailyAt('06:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/daily-tests.log'));

// Schedule cleanup of old test attempts (keep last 90 days)
Schedule::command('model:prune', ['--model' => 'App\\Models\\TestAttempt'])
    ->dailyAt('02:00')
    ->withoutOverlapping();

// Schedule cleanup of completed daily tests older than 1 year
Schedule::call(function () {
    \App\Models\DailyTest::where('is_completed', true)
        ->where('created_at', '<', now()->subYear())
        ->delete();
})
    ->monthlyOn(1, '03:00')
    ->name('cleanup-old-daily-tests')
    ->withoutOverlapping();

// Schedule review reminders (optional - could send notifications)
Schedule::call(function () {
    $usersWithReviews = \App\Models\User::whereHas('userWords', function ($query) {
        $query->where('mistake_count', '>', 0)
            ->where('mastered', false)
            ->where(function ($q) {
                $q->whereNull('next_review_at')
                    ->orWhere('next_review_at', '<=', now());
            });
    })
    ->whereDoesntHave('dailyTests', function ($query) {
        $query->whereDate('date', today());
    })
    ->get();

    foreach ($usersWithReviews as $user) {
        // Here you could send email notifications or push notifications
        \Illuminate\Support\Facades\Log::info('User has pending reviews', [
            'user_id' => $user->id,
            'review_count' => $user->reviewWords()->count(),
        ]);
    }
})
    ->dailyAt('18:00')
    ->name('review-reminders');
