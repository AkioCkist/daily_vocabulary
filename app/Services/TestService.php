<?php

namespace App\Services;

use App\Models\DailyTest;
use App\Models\DailyTestItem;
use App\Models\TestAttempt;
use App\Models\User;
use App\Models\Word;
use App\Repositories\Interfaces\WordRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling daily test operations.
 */
class TestService
{
    public function __construct(
        private WordRepositoryInterface $wordRepository,
        private UserProgressService $userProgressService
    ) {}

    /**
     * Generate daily test for user.
     *
     * @param User $user
     * @param array<string, mixed> $config
     * @return DailyTest
     */
    public function generateDailyTest(User $user, array $config = []): DailyTest
    {
        $isCustomTest = !empty($config['cefr_level']) || !empty($config['topic']);
        
        // Check if test already exists for today (only for daily tests without custom config)
        if (!$isCustomTest) {
            $existingTest = DailyTest::todayForUser($user->id)->first();
            if ($existingTest) {
                return $existingTest;
            }
        } else {
            // For custom tests, delete any existing test for today to avoid conflicts
            DailyTest::where('user_id', $user->id)
                ->where('date', today())
                ->delete();
        }

        $testLength = $config['question_count'] ?? $config['test_length'] ?? 10;
        $newWordsRatio = $config['new_words_ratio'] ?? 0.4;
        $reviewWordsRatio = $config['review_words_ratio'] ?? 0.4;
        $unmasteredWordsRatio = $config['unmastered_words_ratio'] ?? 0.2;

        return DB::transaction(function () use ($user, $testLength, $newWordsRatio, $reviewWordsRatio, $unmasteredWordsRatio, $config) {
            // Create the daily test
            $dailyTest = DailyTest::create([
                'user_id' => $user->id,
                'date' => today(),
                'meta' => $config,
                'is_completed' => false,
            ]);

            // Extract filters from config
            $filters = [];
            if (!empty($config['cefr_level']) && $config['cefr_level'] !== '') {
                $filters['cefr_level'] = $config['cefr_level'];
            }
            if (!empty($config['topic']) && $config['topic'] !== '') {
                $filters['topic'] = $config['topic'];
            }

            // If we have specific filters, get words only from those filters
            if (!empty($filters)) {
                Log::info('Generating test with filters', ['filters' => $filters, 'testLength' => $testLength]);
                
                // First, check how many words are available with these filters
                $totalAvailable = Word::filter($filters)->count();
                Log::info('Words available with filters', ['count' => $totalAvailable]);
                
                if ($totalAvailable == 0) {
                    throw new \Exception("No words found matching your selected filters. Please try different filter combinations.");
                }
                
                if ($totalAvailable < $testLength) {
                    throw new \Exception("Not enough words available for your selected filters. Found {$totalAvailable} words, but you requested {$testLength} questions. Please try a smaller test size or different filters.");
                }
                
                // Get words directly with a limit instead of the complex random logic
                $allWords = Word::filter($filters)
                    ->inRandomOrder()
                    ->limit($testLength)
                    ->get();
                
                Log::info('Retrieved words for test', ['count' => $allWords->count()]);
            } else {
                // Use the original logic for unfiltered tests (daily tests)
                $newWordsCount = (int) round($testLength * $newWordsRatio);
                $reviewWordsCount = (int) round($testLength * $reviewWordsRatio);
                $unmasteredWordsCount = $testLength - $newWordsCount - $reviewWordsCount;

                // Get words from each category
                $newWords = $this->wordRepository->getNewWordsForUser($user->id, [], $newWordsCount);
                $reviewWords = $this->wordRepository->getReviewWordsForUser($user->id, $reviewWordsCount);
                $unmasteredWords = $this->wordRepository->getUnmasteredWordsForUser($user->id, $unmasteredWordsCount);

                // Combine and shuffle words
                $allWords = $newWords->merge($reviewWords)->merge($unmasteredWords);
            }
            
            $allWords = $allWords->shuffle();

            // Create test items
            foreach ($allWords as $word) {
                $this->createTestItem($dailyTest, $word);
            }

            return $dailyTest;
        });
    }

    /**
     * Create a test item for a word.
     */
    private function createTestItem(DailyTest $dailyTest, Word $word): DailyTestItem
    {
        // Randomly choose question type
        $questionType = rand(0, 1) === 0 
            ? DailyTestItem::QUESTION_TYPE_WORD_TO_DEFINITION
            : DailyTestItem::QUESTION_TYPE_DEFINITION_TO_WORD;

        $options = null;
        $correctAnswer = '';

        if ($questionType === DailyTestItem::QUESTION_TYPE_WORD_TO_DEFINITION) {
            $correctAnswer = $word->definition;
            $options = $this->generateDefinitionOptions($word);
        } else {
            $correctAnswer = $word->word;
            // For word input, no multiple choice options needed
        }

        return DailyTestItem::create([
            'daily_test_id' => $dailyTest->id,
            'word_id' => $word->id,
            'question_type' => $questionType,
            'options' => $options,
            'correct_answer' => $correctAnswer,
        ]);
    }

    /**
     * Generate multiple choice options for definition questions.
     *
     * @param Word $word
     * @return array<string>
     */
    private function generateDefinitionOptions(Word $word): array
    {
        $options = [$word->definition];
        
        // Get 3 random wrong definitions from real words only
        $wrongDefinitions = Word::where('id', '!=', $word->id)
            ->where('cefr_level', $word->cefr_level) // Same difficulty level
            ->where('definition', 'not like', '%Auto-generated%')
            ->where('definition', 'not like', '%dolor%')
            ->where('definition', 'not like', '%Lorem%')
            ->where('word', 'not like', 'word_%')
            ->inRandomOrder()
            ->limit(3)
            ->pluck('definition')
            ->toArray();

        $options = array_merge($options, $wrongDefinitions);
        shuffle($options);

        return $options;
    }

    /**
     * Submit answer for a test item.
     *
     * @param User $user
     * @param int $testItemId
     * @param string $answer
     * @param int|null $timeTaken
     * @return TestAttempt
     */
    public function submitAnswer(User $user, int $testItemId, string $answer, ?int $timeTaken = null): TestAttempt
    {
        $testItem = DailyTestItem::with(['dailyTest', 'word'])->findOrFail($testItemId);
        
        // Verify the test belongs to the user
        if ($testItem->dailyTest->user_id !== $user->id) {
            throw new \Exception('Test item does not belong to the user');
        }

        $isCorrect = $this->evaluateAnswer($testItem, $answer);

        return DB::transaction(function () use ($user, $testItem, $answer, $timeTaken, $isCorrect) {
            // Create test attempt
            $attempt = TestAttempt::create([
                'user_id' => $user->id,
                'word_id' => $testItem->word_id,
                'daily_test_id' => $testItem->daily_test_id,
                'daily_test_item_id' => $testItem->id,
                'is_correct' => $isCorrect,
                'answer_text' => $answer,
                'time_taken' => $timeTaken,
            ]);

            // Update user progress
            $this->userProgressService->updateWordProgress($user, $testItem->word_id, $isCorrect);

            // Update test item result
            $testItem->update([
                'result' => [
                    'user_answer' => $answer,
                    'is_correct' => $isCorrect,
                    'time_taken' => $timeTaken,
                    'submitted_at' => now()->toISOString(),
                ]
            ]);

            return $attempt;
        });
    }

    /**
     * Evaluate if the answer is correct.
     */
    private function evaluateAnswer(DailyTestItem $testItem, string $answer): bool
    {
        $answer = trim(strtolower($answer));
        $correctAnswer = trim(strtolower($testItem->correct_answer));

        // For definition to word questions, be more flexible with matching
        if ($testItem->question_type === DailyTestItem::QUESTION_TYPE_DEFINITION_TO_WORD) {
            // Check exact match first
            if ($answer === $correctAnswer) {
                return true;
            }

            // Check similarity for typos (simple version)
            $similarity = 0;
            similar_text($answer, $correctAnswer, $similarity);
            
            // Accept if 85% similar
            return $similarity >= 85.0;
        }

        // For word to definition questions (multiple choice)
        return $answer === $correctAnswer;
    }

    /**
     * Get today's test for user.
     */
    public function getTodaysTest(User $user): ?DailyTest
    {
        return DailyTest::todayForUser($user->id)
            ->with(['items.word', 'attempts'])
            ->first();
    }

    /**
     * Complete the daily test and calculate final score.
     */
    public function completeTest(DailyTest $dailyTest): DailyTest
    {
        $dailyTest->markCompleted();
        return $dailyTest->fresh();
    }

    /**
     * Get test statistics for user.
     *
     * @param User $user
     * @return array<string, mixed>
     */
    public function getTestStats(User $user): array
    {
        $totalTests = DailyTest::where('user_id', $user->id)->count();
        $completedTests = DailyTest::where('user_id', $user->id)
            ->where('is_completed', true)
            ->count();

        $averageScore = DailyTest::where('user_id', $user->id)
            ->where('is_completed', true)
            ->whereNotNull('score')
            ->avg('score') ?? 0;

        $recentScores = DailyTest::where('user_id', $user->id)
            ->where('is_completed', true)
            ->whereNotNull('score')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->pluck('score')
            ->toArray();

        return [
            'total_tests' => $totalTests,
            'completed_tests' => $completedTests,
            'completion_rate' => $totalTests > 0 ? round(($completedTests / $totalTests) * 100, 2) : 0,
            'average_score' => round($averageScore, 2),
            'recent_scores' => $recentScores,
            'current_streak' => $this->getCurrentTestStreak($user),
        ];
    }

    /**
     * Get current consecutive test completion streak.
     */
    private function getCurrentTestStreak(User $user): int
    {
        $recentTests = DailyTest::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        $streak = 0;
        $currentDate = today();

        foreach ($recentTests as $test) {
            if ($test->date->equalTo($currentDate) && $test->is_completed) {
                $streak++;
                $currentDate = $currentDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }
}