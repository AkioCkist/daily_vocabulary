<?php 


namespace App\Services;

use App\Repositories\Interfaces\UserWordRepositoryInterface;

/**
 * Service for managing user vocabulary actions.
 *
 * @package App\Services
 */
class UserVocabularyService
{
    /**
     * Status constant for learned words.
     */
    public const STATUS_LEARNED = 'learned';

    /**
     * UserVocabularyService constructor.
     *
     * @param UserWordRepositoryInterface $repo
     */
    public function __construct(
        protected UserWordRepositoryInterface $repo
    ) {}

    /**
     * Add a word to the user's vocabulary.
     *
     * @param int $userId
     * @param int $wordId
     * @return mixed
     */
    public function addWord(int $userId, int $wordId)
    {
        return $this->repo->addWordToUser($userId, $wordId);
    }

    /**
     * Remove a word from the user's vocabulary.
     *
     * @param int $userId
     * @param int $wordId
     * @return mixed
     */
    public function removeWord(int $userId, int $wordId)
    {
        return $this->repo->removeWord($userId, $wordId);
    }

    /**
     * Mark a word as learned for the user.
     *
     * @param int $userId
     * @param int $wordId
     * @return mixed
     */
    public function markLearned(int $userId, int $wordId)
    {
        return $this->repo->updateStatus($userId, $wordId, self::STATUS_LEARNED);
    }

    /**
     * Get all words for a user, optionally filtered by status.
     *
     * @param int $userId
     * @param string|null $status
     * @return mixed
     */
    public function getUserWords(int $userId, ?string $status = null)
    {
        return $this->repo->getUserWords($userId, $status);
    }
}
