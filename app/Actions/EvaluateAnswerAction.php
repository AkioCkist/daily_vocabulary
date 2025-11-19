<?php

namespace App\Actions;

use App\Models\DailyTestItem;
use App\Models\TestAttempt;
use App\Models\User;
use App\Services\UserProgressService;
use Illuminate\Support\Facades\DB;

/**
 * Action class for evaluating test answers.
 */
class EvaluateAnswerAction
{
    public function __construct(
        private UserProgressService $userProgressService
    ) {}

    /**
     * Execute the answer evaluation.
     *
     * @param User $user
     * @param int $testItemId
     * @param string $answer
     * @param int|null $timeTaken
     * @return TestAttempt
     */
    public function execute(User $user, int $testItemId, string $answer, ?int $timeTaken = null): TestAttempt
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
                    'evaluation_details' => $this->getEvaluationDetails($testItem, $answer, $isCorrect),
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
        $answer = trim($answer);
        $correctAnswer = trim($testItem->correct_answer);

        // For definition to word questions, be more flexible with matching
        if ($testItem->question_type === DailyTestItem::QUESTION_TYPE_DEFINITION_TO_WORD) {
            return $this->evaluateWordAnswer($answer, $correctAnswer);
        }

        // For word to definition questions (multiple choice)
        return $this->evaluateDefinitionAnswer($answer, $correctAnswer);
    }

    /**
     * Evaluate word answer with flexibility for typos.
     */
    private function evaluateWordAnswer(string $answer, string $correctAnswer): bool
    {
        $answer = strtolower($answer);
        $correctAnswer = strtolower($correctAnswer);

        // Check exact match first
        if ($answer === $correctAnswer) {
            return true;
        }

        // Check for common variations
        if ($this->checkCommonVariations($answer, $correctAnswer)) {
            return true;
        }

        // Check similarity for typos
        $similarity = 0;
        similar_text($answer, $correctAnswer, $similarity);
        
        // Accept if 85% similar for longer words, 90% for shorter words
        $threshold = strlen($correctAnswer) > 6 ? 85.0 : 90.0;
        
        return $similarity >= $threshold;
    }

    /**
     * Check for common word variations.
     */
    private function checkCommonVariations(string $answer, string $correctAnswer): bool
    {
        // Remove common prefixes/suffixes for comparison
        $variations = [
            // Remove articles
            ['the ', ''],
            ['a ', ''],
            ['an ', ''],
            // Handle plurals
            ['s', ''],
            ['es', ''],
            ['ies', 'y'],
            // Handle verb forms
            ['ed', ''],
            ['ing', ''],
            ['er', ''],
            ['est', ''],
        ];

        foreach ($variations as [$from, $to]) {
            $modifiedAnswer = str_replace($from, $to, $answer);
            $modifiedCorrect = str_replace($from, $to, $correctAnswer);
            
            if ($modifiedAnswer === $modifiedCorrect) {
                return true;
            }
        }

        return false;
    }

    /**
     * Evaluate definition answer (exact match required).
     */
    private function evaluateDefinitionAnswer(string $answer, string $correctAnswer): bool
    {
        return strtolower(trim($answer)) === strtolower(trim($correctAnswer));
    }

    /**
     * Get detailed evaluation information.
     *
     * @param DailyTestItem $testItem
     * @param string $answer
     * @param bool $isCorrect
     * @return array<string, mixed>
     */
    private function getEvaluationDetails(DailyTestItem $testItem, string $answer, bool $isCorrect): array
    {
        $details = [
            'question_type' => $testItem->question_type,
            'user_answer' => $answer,
            'correct_answer' => $testItem->correct_answer,
            'is_correct' => $isCorrect,
            'evaluation_method' => 'exact_match',
        ];

        if (!$isCorrect && $testItem->question_type === DailyTestItem::QUESTION_TYPE_DEFINITION_TO_WORD) {
            // Add similarity score for word answers
            $similarity = 0;
            similar_text(strtolower($answer), strtolower($testItem->correct_answer), $similarity);
            $details['similarity_score'] = $similarity;
            $details['evaluation_method'] = 'similarity_based';
        }

        return $details;
    }
}