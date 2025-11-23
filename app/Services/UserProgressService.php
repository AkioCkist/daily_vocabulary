<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserWord;
use App\Models\TestAttempt;
use Illuminate\Support\Facades\DB;

/**
 * Service for managing user progress and statistics.
 */
class UserProgressService
{
    /**
     * Update user's word progress after test or learning session.
     */
    public function updateWordProgress(User $user, int $wordId, bool $isCorrect): UserWord
    {
        return DB::transaction(function () use ($user, $wordId, $isCorrect) {
            $userWord = UserWord::firstOrCreate(
                ['user_id' => $user->id, 'word_id' => $wordId],
                [
                    'is_learned' => false,
                    'mastered' => false,
                    'consecutive_correct' => 0,
                    'mistake_count' => 0,
                ]
            );

            if ($isCorrect) {
                $userWord->handleCorrectAnswer();
            } else {
                $userWord->handleIncorrectAnswer();
            }

            // Invalidate user's progress cache
            \Illuminate\Support\Facades\Cache::forget("user:progress:{$user->id}");

            return $userWord;
        });
    }

    /**
     * Get comprehensive user progress statistics with Redis caching.
     *
     * @param User $user
     * @return array<string, mixed>
     */
    public function getUserProgressStats(User $user): array
    {
        return \Illuminate\Support\Facades\Cache::remember(
            "user:progress:{$user->id}",
            now()->addHours(6), // Cache for 6 hours as stats update regularly
            function () use ($user) {
                $userWords = UserWord::where('user_id', $user->id);
                $testAttempts = TestAttempt::where('user_id', $user->id);

                return [
                    'vocabulary_stats' => [
                        'total_words_seen' => $userWords->count(),
                        'words_learned' => $userWords->clone()->where('is_learned', true)->count(),
                        'words_mastered' => $userWords->clone()->where('mastered', true)->count(),
                        'words_in_review' => $userWords->clone()->needsReview()->count(),
                        'average_consecutive_correct' => $userWords->clone()->avg('consecutive_correct') ?? 0,
                        'total_mistakes' => $userWords->clone()->sum('mistake_count'),
                    ],
                    'test_performance' => [
                        'total_attempts' => $testAttempts->count(),
                        'correct_answers' => $testAttempts->clone()->where('is_correct', true)->count(),
                        'accuracy_rate' => $this->calculateAccuracyRate($user),
                        'recent_test_scores' => $this->getRecentTestScores($user),
                    ],
                    'learning_patterns' => [
                        'most_difficult_topics' => $this->getMostDifficultTopics($user),
                        'strongest_cefr_levels' => $this->getStrongestCefrLevels($user),
                        'learning_streak' => $this->getCurrentLearningStreak($user),
                    ],
                ];
            }
        );
    }

    /**
     * Calculate overall accuracy rate for user.
     */
    private function calculateAccuracyRate(User $user): float
    {
        $totalAttempts = TestAttempt::where('user_id', $user->id)->count();
        
        if ($totalAttempts === 0) {
            return 0.0;
        }

        $correctAttempts = TestAttempt::where('user_id', $user->id)
            ->where('is_correct', true)
            ->count();

        return round(($correctAttempts / $totalAttempts) * 100, 2);
    }

    /**
     * Get recent test scores.
     *
     * @param User $user
     * @param int $limit
     * @return array<int, int>
     */
    private function getRecentTestScores(User $user, int $limit = 10): array
    {
        return DB::table('daily_tests')
            ->where('user_id', $user->id)
            ->whereNotNull('score')
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->pluck('score')
            ->toArray();
    }

    /**
     * Get topics that the user struggles with most.
     *
     * @param User $user
     * @param int $limit
     * @return array<string, mixed>
     */
    private function getMostDifficultTopics(User $user, int $limit = 5): array
    {
        return DB::table('user_words')
            ->join('words', 'user_words.word_id', '=', 'words.id')
            ->where('user_words.user_id', $user->id)
            ->select('words.topic')
            ->selectRaw('AVG(user_words.mistake_count) as avg_mistakes')
            ->selectRaw('AVG(user_words.consecutive_correct) as avg_correct')
            ->groupBy('words.topic')
            ->orderBy('avg_mistakes', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get CEFR levels where user performs best.
     *
     * @param User $user
     * @param int $limit
     * @return array<string, mixed>
     */
    private function getStrongestCefrLevels(User $user, int $limit = 3): array
    {
        return DB::table('user_words')
            ->join('words', 'user_words.word_id', '=', 'words.id')
            ->where('user_words.user_id', $user->id)
            ->select('words.cefr_level')
            ->selectRaw('AVG(user_words.consecutive_correct) as avg_correct')
            ->selectRaw('AVG(user_words.mistake_count) as avg_mistakes')
            ->groupBy('words.cefr_level')
            ->orderBy('avg_correct', 'desc')
            ->orderBy('avg_mistakes', 'asc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Calculate current learning streak (days).
     */
    private function getCurrentLearningStreak(User $user): int
    {
        // Get consecutive days with learning activity
        $recentActivity = DB::table('test_attempts')
            ->where('user_id', $user->id)
            ->selectRaw('DATE(created_at) as activity_date')
            ->distinct()
            ->orderBy('activity_date', 'desc')
            ->limit(30) // Check last 30 days
            ->pluck('activity_date')
            ->toArray();

        if (empty($recentActivity)) {
            return 0;
        }

        $streak = 0;
        $today = now()->format('Y-m-d');
        $currentDate = $today;

        foreach ($recentActivity as $activityDate) {
            if ($activityDate === $currentDate) {
                $streak++;
                $currentDate = now()->subDays($streak)->format('Y-m-d');
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Get words that need immediate review.
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWordsNeedingReview(User $user)
    {
        return UserWord::where('user_id', $user->id)
            ->needsReview()
            ->with('word')
            ->get();
    }

    /**
     * Reset progress for a specific word.
     */
    public function resetWordProgress(User $user, int $wordId): void
    {
        UserWord::where('user_id', $user->id)
            ->where('word_id', $wordId)
            ->update([
                'is_learned' => false,
                'mastered' => false,
                'consecutive_correct' => 0,
                'mistake_count' => 0,
                'next_review_at' => null,
                'last_seen_at' => null,
            ]);
    }
}