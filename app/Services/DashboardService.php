<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserWord;
use App\Models\TestAttempt;
use App\Models\Word;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Service for providing comprehensive dashboard data and statistics.
 */
class DashboardService
{
    /**
     * Get comprehensive dashboard data for a user.
     *
     * @param User $user
     * @return array<string, mixed>
     */
    public function getDashboardData(User $user): array
    {
        return \Illuminate\Support\Facades\Cache::remember(
            "dashboard:data:{$user->id}",
            now()->addHours(1), // Cache for 1 hour
            function () use ($user) {
                return [
                    'stats' => $this->getUserStats($user),
                    'learning_heatmap' => $this->getLearningHeatmapData($user),
                    'recent_activity' => $this->getRecentActivity($user),
                    'performance_trends' => $this->getPerformanceTrends($user),
                    'available_topics' => $this->getAvailableTopics($user),
                    'cefr_levels' => $this->getCefrLevels(),
                ];
            }
        );
    }

    /**
     * Get user learning statistics.
     *
     * @param User $user
     * @return array<string, mixed>
     */
    public function getUserStats(User $user): array
    {
        return \Illuminate\Support\Facades\Cache::remember(
            "dashboard:stats:{$user->id}",
            now()->addHours(1), // Cache for 1 hour
            function () use ($user) {
                $userWords = UserWord::where('user_id', $user->id);
                $testAttempts = TestAttempt::where('user_id', $user->id);

                $totalAttempts = $testAttempts->count();
                $correctAttempts = $testAttempts->clone()->where('is_correct', true)->count();
                $accuracyRate = $totalAttempts > 0 ? round(($correctAttempts / $totalAttempts) * 100, 1) : 0;

                return [
                    'words_learning' => $userWords->clone()->where('is_learned', false)->count(),
                    'words_learned' => $userWords->clone()->where('is_learned', true)->count(),
                    'words_mastered' => $userWords->clone()->where('mastered', true)->count(),
                    'total_words_seen' => $userWords->count(),
                    'accuracy_rate' => $accuracyRate,
                    'correct_answers' => $correctAttempts,
                    'total_attempts' => $totalAttempts,
                    'learning_streak' => $this->getCurrentLearningStreak($user),
                    'words_due_for_review' => $userWords->clone()->needsReview()->count(),
                ];
            }
        );
    }

    /**
     * Get learning heatmap data for the past year.
     *
     * @param User $user
     * @return array<string, mixed>
     */
    public function getLearningHeatmapData(User $user): array
    {
        $startDate = Carbon::now()->subYear();
        $endDate = Carbon::now();

        // Get daily learning activity  
        $dailyActivity = TestAttempt::where('user_id', $user->id)
            ->select(
                DB::raw('created_at::date as date'),
                DB::raw('COUNT(*) as attempts'),
                DB::raw('SUM(CASE WHEN is_correct = true THEN 1 ELSE 0 END) as correct')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('created_at::date'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Generate all dates in the range
        $heatmapData = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateString = $currentDate->format('Y-m-d');
            $activity = $dailyActivity->get($dateString);

            $heatmapData[] = [
                'date' => $dateString,
                'attempts' => $activity ? $activity->attempts : 0,
                'correct' => $activity ? $activity->correct : 0,
                'accuracy' => $activity && $activity->attempts > 0
                    ? round(($activity->correct / $activity->attempts) * 100, 1)
                    : 0,
                'level' => $this->getHeatmapLevel($activity ? $activity->attempts : 0),
            ];

            $currentDate->addDay();
        }

        return [
            'data' => $heatmapData,
            'summary' => [
                'total_active_days' => collect($heatmapData)->where('attempts', '>', 0)->count(),
                'longest_streak' => $this->calculateLongestStreak($heatmapData),
                'current_streak' => $this->getCurrentStreakFromData($heatmapData),
            ],
        ];
    }

    /**
     * Get recent learning activity.
     *
     * @param User $user
     * @return array<int, array<string, mixed>>
     */
    public function getRecentActivity(User $user): array
    {
        return TestAttempt::where('user_id', $user->id)
            ->with(['word'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($attempt) {
                return [
                    'word' => $attempt->word->word,
                    'is_correct' => $attempt->is_correct,
                    'created_at' => $attempt->created_at->diffForHumans(),
                    'cefr_level' => $attempt->word->cefr_level,
                    'topic' => $attempt->word->topic,
                ];
            })
            ->toArray();
    }

    /**
     * Get performance trends over time.
     *
     * @param User $user
     * @return array<string, mixed>
     */
    public function getPerformanceTrends(User $user): array
    {
        $trends = TestAttempt::where('user_id', $user->id)
            ->select(
                DB::raw('created_at::date as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN is_correct = true THEN 1 ELSE 0 END) as correct')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy(DB::raw('created_at::date'))
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'accuracy' => $item->total > 0 ? round(($item->correct / $item->total) * 100, 1) : 0,
                    'attempts' => $item->total,
                ];
            });

        return [
            'daily_trends' => $trends->toArray(),
            'average_accuracy' => $trends->avg('accuracy') ?? 0,
            'trend_direction' => $this->calculateTrendDirection($trends),
        ];
    }

    /**
     * Get available topics for the user.
     *
     * @param User $user
     * @return array<string, mixed>
     */
    public function getAvailableTopics(User $user): array
    {
        $systemTopics = Topic::where('is_system', true)
            ->withCount('words')
            ->orderBy('name')
            ->get()
            ->map(function ($topic) {
                return [
                    'id' => $topic->id,
                    'name' => $topic->name,
                    'description' => $topic->description,
                    'words_count' => $topic->words_count,
                ];
            });

        // For user topics, count words from user_word_topics pivot table
        $userTopics = Topic::where('user_id', $user->id)
            ->withCount([
                'collectedWords' => function ($query) use ($user) {
                    $query->where('user_word_topics.user_id', $user->id);
                }
            ])
            ->orderBy('name')
            ->get()
            ->map(function ($topic) {
                return [
                    'id' => $topic->id,
                    'name' => $topic->name,
                    'description' => $topic->description,
                    'words_count' => $topic->collected_words_count,
                ];
            });

        return [
            'system' => $systemTopics->toArray(),
            'user' => $userTopics->toArray(),
        ];
    }

    /**
     * Get available CEFR levels.
     *
     * @return array<int, string>
     */
    public function getCefrLevels(): array
    {
        return [
            'A1' => 'Beginner',
            'A2' => 'Elementary',
            'B1' => 'Intermediate',
            'B2' => 'Upper Intermediate',
            'C1' => 'Advanced',
            'C2' => 'Proficient',
        ];
    }

    /**
     * Calculate current learning streak.
     *
     * @param User $user
     * @return int
     */
    private function getCurrentLearningStreak(User $user): int
    {
        $dates = TestAttempt::where('user_id', $user->id)
            ->select(DB::raw('created_at::date as date'))
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->toArray();

        if (empty($dates)) {
            return 0;
        }

        $streak = 0;
        $currentDate = Carbon::now()->format('Y-m-d');

        foreach ($dates as $date) {
            if ($date === $currentDate) {
                $streak++;
                $currentDate = Carbon::parse($currentDate)->subDay()->format('Y-m-d');
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Get heatmap intensity level based on attempts.
     *
     * @param int $attempts
     * @return int
     */
    private function getHeatmapLevel(int $attempts): int
    {
        if ($attempts === 0)
            return 0;
        if ($attempts <= 5)
            return 1;
        if ($attempts <= 15)
            return 2;
        if ($attempts <= 30)
            return 3;
        return 4;
    }

    /**
     * Calculate longest streak from heatmap data.
     *
     * @param array<int, array<string, mixed>> $heatmapData
     * @return int
     */
    private function calculateLongestStreak(array $heatmapData): int
    {
        $maxStreak = 0;
        $currentStreak = 0;

        foreach ($heatmapData as $day) {
            if ($day['attempts'] > 0) {
                $currentStreak++;
                $maxStreak = max($maxStreak, $currentStreak);
            } else {
                $currentStreak = 0;
            }
        }

        return $maxStreak;
    }

    /**
     * Get current streak from heatmap data.
     *
     * @param array<int, array<string, mixed>> $heatmapData
     * @return int
     */
    private function getCurrentStreakFromData(array $heatmapData): int
    {
        $streak = 0;
        $reversedData = array_reverse($heatmapData);

        foreach ($reversedData as $day) {
            if ($day['attempts'] > 0) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Calculate trend direction from performance data.
     *
     * @param \Illuminate\Support\Collection $trends
     * @return string
     */
    private function calculateTrendDirection($trends): string
    {
        if ($trends->count() < 2) {
            return 'stable';
        }

        $recent = $trends->slice(-7)->avg('accuracy');
        $previous = $trends->slice(-14, 7)->avg('accuracy');

        if ($recent > $previous + 5)
            return 'up';
        if ($recent < $previous - 5)
            return 'down';
        return 'stable';
    }

    /**
     * Get user statistics filtered by day range.
     *
     * @param User $user
     * @param int $days Number of days to look back (1, 7, or 30)
     * @return array<string, mixed>
     */
    public function getUserStatsByDayRange(User $user, int $days): array
    {
        $startDate = Carbon::now()->subDays($days)->startOfDay();

        // Get test attempts within the date range
        $testAttempts = TestAttempt::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate);

        // Count unique study sessions (group by date)
        $studySessions = TestAttempt::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('created_at::date as date'))
            ->distinct()
            ->count();

        // Get words learned within the date range
        $wordsLearned = UserWord::where('user_id', $user->id)
            ->where('is_learned', true)
            ->where('updated_at', '>=', $startDate)
            ->count();

        // Get correct and incorrect answers
        $correctAnswers = $testAttempts->clone()->where('is_correct', true)->count();
        $incorrectAnswers = $testAttempts->clone()->where('is_correct', false)->count();

        // Get streak days within the range
        $streakDays = $this->getStreakWithinRange($user, $days);

        return [
            'total_study_sessions' => $studySessions,
            'total_words_learned' => $wordsLearned,
            'correct_answers' => $correctAnswers,
            'incorrect_answers' => $incorrectAnswers,
            'streak_days' => $streakDays,
        ];
    }

    /**
     * Calculate streak within a specific day range.
     *
     * @param User $user
     * @param int $days
     * @return int
     */
    private function getStreakWithinRange(User $user, int $days): int
    {
        $startDate = Carbon::now()->subDays($days)->startOfDay();

        $dates = TestAttempt::where('user_id', $user->id)
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('created_at::date as date'))
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->toArray();

        if (empty($dates)) {
            return 0;
        }

        $streak = 0;
        $currentDate = Carbon::now()->format('Y-m-d');

        foreach ($dates as $date) {
            if ($date === $currentDate) {
                $streak++;
                $currentDate = Carbon::parse($currentDate)->subDay()->format('Y-m-d');
            } else {
                break;
            }
        }

        return $streak;
    }
}