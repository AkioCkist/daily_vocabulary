<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserWord;
use App\Models\Word;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service for handling review and practice operations.
 */
class ReviewService
{
    public function __construct(
        private UserProgressService $userProgressService
    ) {}

    /**
     * Get words that need review for a user.
     *
     * @param User $user
     * @param int $limit
     * @return Collection<int, UserWord>
     */
    public function getReviewWords(User $user, int $limit = 20): Collection
    {
        return UserWord::where('user_id', $user->id)
            ->needsReview()
            ->with('word')
            ->orderBy('next_review_at', 'asc')
            ->orderBy('mistake_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get a random word from user's review list.
     */
    public function getRandomReviewWord(User $user): ?UserWord
    {
        return UserWord::where('user_id', $user->id)
            ->needsReview()
            ->with('word')
            ->inRandomOrder()
            ->first();
    }

    /**
     * Submit answer for a review practice.
     *
     * @param User $user
     * @param int $wordId
     * @param string $answer
     * @param string $questionType
     * @return array<string, mixed>
     */
    public function submitReviewAnswer(User $user, int $wordId, string $answer, string $questionType): array
    {
        $userWord = UserWord::where('user_id', $user->id)
            ->where('word_id', $wordId)
            ->with('word')
            ->firstOrFail();

        $isCorrect = $this->evaluateReviewAnswer($userWord->word, $answer, $questionType);

        // Update user progress
        $this->userProgressService->updateWordProgress($user, $wordId, $isCorrect);

        // Refresh the model to get updated values
        $userWord->refresh();

        // Invalidate review progress cache since data has changed
        \Illuminate\Support\Facades\Cache::forget("review:progress:{$user->id}");

        return [
            'is_correct' => $isCorrect,
            'correct_answer' => $this->getCorrectAnswer($userWord->word, $questionType),
            'user_word' => $userWord,
            'mastered' => $userWord->mastered,
            'consecutive_correct' => $userWord->consecutive_correct,
        ];
    }

    /**
     * Evaluate review answer.
     */
    private function evaluateReviewAnswer(Word $word, string $answer, string $questionType): bool
    {
        $answer = trim(strtolower($answer));

        switch ($questionType) {
            case 'word_to_definition':
                $correctAnswer = trim(strtolower($word->definition));
                break;
            case 'definition_to_word':
                $correctAnswer = trim(strtolower($word->word));
                // Be more flexible with word answers
                if ($answer === $correctAnswer) {
                    return true;
                }
                // Check similarity for typos
                $similarity = 0;
                similar_text($answer, $correctAnswer, $similarity);
                return $similarity >= 85.0;
            case 'word_to_meaning':
                $correctAnswer = trim(strtolower($word->meaning ?? ''));
                break;
            default:
                return false;
        }

        return $answer === $correctAnswer;
    }

    /**
     * Get correct answer for question type.
     */
    private function getCorrectAnswer(Word $word, string $questionType): string
    {
        return match ($questionType) {
            'word_to_definition' => $word->definition,
            'definition_to_word' => $word->word,
            'word_to_meaning' => $word->meaning ?? '',
            default => '',
        };
    }

    /**
     * Get review session progress.
     *
     * @param User $user
     * @return array<string, mixed>
     */
    public function getReviewProgress(User $user): array
    {
        return \Illuminate\Support\Facades\Cache::remember(
            "review:progress:{$user->id}",
            now()->addHours(1), // Cache for 1 hour
            function () use ($user) {
                $totalReviewWords = UserWord::where('user_id', $user->id)
                    ->needsReview()
                    ->count();

                $almostMasteredWords = UserWord::where('user_id', $user->id)
                    ->needsReview()
                    ->where('consecutive_correct', '>=', 2)
                    ->count();

                $strugglingWords = UserWord::where('user_id', $user->id)
                    ->needsReview()
                    ->where('mistake_count', '>=', 5)
                    ->count();

                $recentlyAddedWords = UserWord::where('user_id', $user->id)
                    ->needsReview()
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count();

                return [
                    'total_review_words' => $totalReviewWords,
                    'almost_mastered' => $almostMasteredWords,
                    'struggling_words' => $strugglingWords,
                    'recently_added' => $recentlyAddedWords,
                    'mastery_rate' => $this->calculateMasteryRate($user),
                ];
            }
        );
    }

    /**
     * Calculate user's mastery rate.
     */
    private function calculateMasteryRate(User $user): float
    {
        $totalWords = UserWord::where('user_id', $user->id)->count();
        
        if ($totalWords === 0) {
            return 0.0;
        }

        $masteredWords = UserWord::where('user_id', $user->id)
            ->where('mastered', true)
            ->count();

        return round(($masteredWords / $totalWords) * 100, 2);
    }

    /**
     * Start intensive review session for struggling words.
     *
     * @param User $user
     * @param int $minMistakes
     * @return Collection<int, UserWord>
     */
    public function getIntensiveReviewWords(User $user, int $minMistakes = 3): Collection
    {
        return UserWord::where('user_id', $user->id)
            ->needsReview()
            ->where('mistake_count', '>=', $minMistakes)
            ->with('word')
            ->orderBy('mistake_count', 'desc')
            ->orderBy('last_seen_at', 'asc')
            ->get();
    }

    /**
     * Get review statistics by topic.
     *
     * @param User $user
     * @return array<string, mixed>
     */
    public function getReviewStatsByTopic(User $user): array
    {
        return UserWord::where('user_id', $user->id)
            ->needsReview()
            ->join('words', 'user_words.word_id', '=', 'words.id')
            ->select('words.topic')
            ->selectRaw('COUNT(*) as total_words')
            ->selectRaw('AVG(user_words.mistake_count) as avg_mistakes')
            ->selectRaw('AVG(user_words.consecutive_correct) as avg_correct')
            ->groupBy('words.topic')
            ->orderBy('avg_mistakes', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Mark word as manually mastered (admin override).
     */
    public function markWordAsMastered(User $user, int $wordId): UserWord
    {
        $userWord = UserWord::where('user_id', $user->id)
            ->where('word_id', $wordId)
            ->firstOrFail();

        $userWord->update([
            'mastered' => true,
            'consecutive_correct' => 3,
            'mistake_count' => 0,
            'is_learned' => true,
        ]);

        return $userWord;
    }

    /**
     * Reset word to review state (admin override).
     */
    public function resetWordToReview(User $user, int $wordId): UserWord
    {
        $userWord = UserWord::where('user_id', $user->id)
            ->where('word_id', $wordId)
            ->firstOrFail();

        $userWord->update([
            'mastered' => false,
            'consecutive_correct' => 0,
            'mistake_count' => 1,
            'next_review_at' => now(),
        ]);

        return $userWord;
    }

    /**
     * Get spaced repetition schedule for user.
     *
     * @param User $user
     * @return Collection<int, UserWord>
     */
    public function getSpacedRepetitionWords(User $user): Collection
    {
        return UserWord::where('user_id', $user->id)
            ->needsReview()
            ->where('next_review_at', '<=', now())
            ->with('word')
            ->orderBy('next_review_at', 'asc')
            ->get();
    }

    /**
     * Get aggregated review statistics for user (optimized single query).
     * Combines all stat calculations into one aggregate query instead of 4 separate count queries.
     *
     * @param User $user
     * @return array<string, int>
     */
    public function getAggregatedStats(User $user): array
    {
        $stats = \Illuminate\Support\Facades\DB::table('user_words')
            ->where('user_id', $user->id)
            ->selectRaw('COUNT(*) as total_words')
            ->selectRaw('SUM(CASE WHEN is_learned = true THEN 1 ELSE 0 END) as learned_words')
            ->selectRaw('SUM(CASE WHEN mastered != true THEN 1 ELSE 0 END) as review_words')
            ->selectRaw('SUM(CASE WHEN mastered = true THEN 1 ELSE 0 END) as mastered_words')
            ->first();

        return [
            'total_words' => (int) ($stats->total_words ?? 0),
            'learned_words' => (int) ($stats->learned_words ?? 0),
            'review_words' => (int) ($stats->review_words ?? 0),
            'mastered_words' => (int) ($stats->mastered_words ?? 0),
        ];
    }

    /**
     * Get review statistics for user.
     *
     * @param User $user
     * @return array<string, int>
     */
    public function getReviewStats(User $user): array
    {
        $learnedWords = UserWord::where('user_id', $user->id)
            ->where('is_learned', true)
            ->count();

        $reviewWords = UserWord::where('user_id', $user->id)
            ->needsReview()
            ->count();

        $masteredWords = UserWord::where('user_id', $user->id)
            ->where('mastered', true)
            ->count();

        return [
            'learned_words' => $learnedWords,
            'review_words' => $reviewWords,
            'mastered_words' => $masteredWords,
        ];
    }

    /**
     * Rate word difficulty based on user feedback.
     *
     * @param User $user
     * @param int $wordId
     * @param int $difficulty Rating from 1 (hard) to 5 (easy)
     * @return bool
     */
    public function rateWordDifficulty(User $user, int $wordId, int $difficulty): bool
    {
        $userWord = UserWord::where('user_id', $user->id)
            ->where('word_id', $wordId)
            ->first();

        if (!$userWord) {
            return false;
        }

        // Update based on difficulty rating
        $updates = [];
        
        if ($difficulty <= 2) {
            // Hard - increase mistake count, reset next review to sooner
            $updates['mistake_count'] = $userWord->mistake_count + 1;
            $updates['next_review_at'] = now()->addHours(2);
            $updates['consecutive_correct'] = 0;
        } elseif ($difficulty >= 4) {
            // Easy - increase consecutive correct, extend next review
            $updates['consecutive_correct'] = $userWord->consecutive_correct + 1;
            $updates['next_review_at'] = now()->addDays(3);
            
            // Mark as mastered if consistently easy
            if ($updates['consecutive_correct'] >= 3) {
                $updates['mastered'] = true;
                $updates['is_learned'] = true;
            }
        } else {
            // Medium - moderate progress
            $updates['next_review_at'] = now()->addDays(1);
        }

        $updates['last_seen_at'] = now();
        
        $userWord->update($updates);
        
        return true;
    }
}