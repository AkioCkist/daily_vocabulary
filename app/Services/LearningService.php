<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserWord;
use App\Models\Word;
use App\Repositories\Interfaces\WordRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service for handling learning-related operations.
 */
class LearningService
{
    public function __construct(
        private WordRepositoryInterface $wordRepository,
        private UserProgressService $userProgressService
    ) {}

    /**
     * Get next random word for learning session.
     *
     * @param User $user
     * @param array<string, mixed> $filters
     * @param array<int> $excludeIds
     * @return Word|null
     */
    public function getNextRandomWord(User $user, array $filters = [], array $excludeIds = []): ?Word
    {
        // First try to get from filtered results
        $word = $this->wordRepository->getRandomWord($filters);
        
        // If the word is in exclude list, try to get another one
        if ($word && in_array($word->id, $excludeIds)) {
            $word = $this->wordRepository->getRandomWordExcluding($excludeIds);
        }

        return $word;
    }

    /**
     * Mark word as learned for user.
     */
    public function markWordAsLearned(User $user, int $wordId): UserWord
    {
        $userWord = UserWord::firstOrCreate(
            ['user_id' => $user->id, 'word_id' => $wordId],
            [
                'is_learned' => false,
                'mastered' => false,
                'consecutive_correct' => 0,
                'mistake_count' => 0,
            ]
        );

        $userWord->markLearned();

        return $userWord;
    }

    /**
     * Add word to user's review list.
     */
    public function addWordToReview(User $user, int $wordId): UserWord
    {
        $userWord = UserWord::firstOrCreate(
            ['user_id' => $user->id, 'word_id' => $wordId],
            [
                'is_learned' => false,
                'mastered' => false,
                'consecutive_correct' => 0,
                'mistake_count' => 0,
            ]
        );

        $userWord->addToReview();

        return $userWord;
    }

    /**
     * Get learning statistics for user.
     *
     * @param User $user
     * @return array<string, mixed>
     */
    public function getLearningStats(User $user): array
    {
        $stats = [
            'total_words_learned' => UserWord::where('user_id', $user->id)
                ->where('is_learned', true)
                ->count(),
            'words_mastered' => UserWord::where('user_id', $user->id)
                ->where('mastered', true)
                ->count(),
            'words_in_review' => UserWord::where('user_id', $user->id)
                ->where('mistake_count', '>', 0)
                ->where('mastered', false)
                ->count(),
            'consecutive_correct_streak' => $this->getCurrentStreak($user),
            'total_mistakes' => UserWord::where('user_id', $user->id)
                ->sum('mistake_count'),
        ];

        return $stats;
    }

    /**
     * Get user's current learning streak.
     */
    private function getCurrentStreak(User $user): int
    {
        // This is a simplified version - you might want to implement based on daily activity
        return UserWord::where('user_id', $user->id)
            ->max('consecutive_correct') ?? 0;
    }

    /**
     * Get words for learning session based on user's progress.
     *
     * @param User $user
     * @param array<string, mixed> $filters
     * @param int $count
     * @return Collection<int, Word>
     */
    public function getWordsForLearningSession(User $user, array $filters = [], int $count = 10): Collection
    {
        // Prioritize review words, then new words
        $reviewWords = $this->wordRepository->getReviewWordsForUser($user->id, $count);
        
        $remainingCount = $count - $reviewWords->count();
        
        if ($remainingCount > 0) {
            $newWords = $this->wordRepository->getNewWordsForUser($user->id, $filters, $remainingCount);
            return $reviewWords->merge($newWords);
        }

        return $reviewWords;
    }

    /**
     * Update user's progress after learning session.
     *
     * @param User $user
     * @param int $wordId
     * @param bool $isCorrect
     * @return UserWord
     */
    public function updateProgress(User $user, int $wordId, bool $isCorrect): UserWord
    {
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

        return $userWord;
    }
}