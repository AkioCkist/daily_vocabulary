<?php

namespace App\Actions;

use App\Models\DailyTest;
use App\Models\DailyTestItem;
use App\Models\User;
use App\Models\Word;
use App\Repositories\Interfaces\WordRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Action class for generating daily tests.
 */
class GenerateDailyTestAction
{
    public function __construct(
        private WordRepositoryInterface $wordRepository
    ) {}

    /**
     * Execute the daily test generation.
     *
     * @param User $user
     * @param array<string, mixed> $config
     * @return DailyTest
     */
    public function execute(User $user, array $config = []): DailyTest
    {
        // Check if test already exists for today
        $existingTest = DailyTest::todayForUser($user->id)->first();
        if ($existingTest) {
            return $existingTest;
        }

        $testConfig = $this->prepareTestConfiguration($config);
        
        return DB::transaction(function () use ($user, $testConfig) {
            // Create the daily test
            $dailyTest = DailyTest::create([
                'user_id' => $user->id,
                'date' => today(),
                'meta' => $testConfig,
                'is_completed' => false,
            ]);

            // Get words for the test
            $words = $this->selectWordsForTest($user, $testConfig);
            
            // Create test items
            $this->createTestItems($dailyTest, $words);

            return $dailyTest;
        });
    }

    /**
     * Prepare test configuration with defaults.
     *
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private function prepareTestConfiguration(array $config): array
    {
        return array_merge([
            'test_length' => 20,
            'new_words_ratio' => 0.4,
            'review_words_ratio' => 0.4,
            'unmastered_words_ratio' => 0.2,
            'difficulty_distribution' => [
                'A1' => 0.2,
                'A2' => 0.3,
                'B1' => 0.3,
                'B2' => 0.15,
                'C1' => 0.04,
                'C2' => 0.01,
            ],
        ], $config);
    }

    /**
     * Select words for the test based on configuration.
     *
     * @param User $user
     * @param array<string, mixed> $config
     * @return Collection<int, Word>
     */
    private function selectWordsForTest(User $user, array $config): Collection
    {
        $testLength = $config['test_length'];
        $newWordsCount = (int) round($testLength * $config['new_words_ratio']);
        $reviewWordsCount = (int) round($testLength * $config['review_words_ratio']);
        $unmasteredWordsCount = $testLength - $newWordsCount - $reviewWordsCount;

        // Get words from each category
        $newWords = $this->wordRepository->getNewWordsForUser($user->id, [], $newWordsCount);
        $reviewWords = $this->wordRepository->getReviewWordsForUser($user->id, $reviewWordsCount);
        $unmasteredWords = $this->wordRepository->getUnmasteredWordsForUser($user->id, $unmasteredWordsCount);

        // If we don't have enough words in any category, fill from others
        $allWords = $newWords->merge($reviewWords)->merge($unmasteredWords);
        
        // If we still don't have enough words, get more new words
        if ($allWords->count() < $testLength) {
            $needed = $testLength - $allWords->count();
            $existingIds = $allWords->pluck('id')->toArray();
            
            $additionalWords = Word::whereNotIn('id', $existingIds)
                ->inRandomOrder()
                ->limit($needed)
                ->get();
                
            $allWords = $allWords->merge($additionalWords);
        }

        return $allWords->shuffle()->take($testLength);
    }

    /**
     * Create test items for the selected words.
     *
     * @param DailyTest $dailyTest
     * @param Collection<int, Word> $words
     */
    private function createTestItems(DailyTest $dailyTest, Collection $words): void
    {
        foreach ($words as $word) {
            $this->createTestItem($dailyTest, $word);
        }
    }

    /**
     * Create a single test item.
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
        
        // Get wrong definitions from same CEFR level
        $wrongDefinitions = Word::where('id', '!=', $word->id)
            ->where('cefr_level', $word->cefr_level)
            ->inRandomOrder()
            ->limit(3)
            ->pluck('definition')
            ->toArray();

        // If we don't have enough from same level, get from any level
        if (count($wrongDefinitions) < 3) {
            $existingDefinitions = array_merge([$word->definition], $wrongDefinitions);
            $additionalWrong = Word::where('id', '!=', $word->id)
                ->whereNotIn('definition', $existingDefinitions)
                ->inRandomOrder()
                ->limit(3 - count($wrongDefinitions))
                ->pluck('definition')
                ->toArray();
            
            $wrongDefinitions = array_merge($wrongDefinitions, $additionalWrong);
        }

        $options = array_merge($options, $wrongDefinitions);
        shuffle($options);

        return $options;
    }
}