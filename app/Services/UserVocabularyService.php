<?php 

namespace App\Services;

use App\Repositories\Interfaces\UserWordRepositoryInterface;

class UserVocabularyService
{
    public function __construct(
        protected UserWordRepositoryInterface $repo
    ) {}

    public function addWord(int $userId, int $wordId)
    {
        return $this->repo->addWordToUser($userId, $wordId);
    }

    public function removeWord(int $userId, int $wordId)
    {
        return $this->repo->removeWord($userId, $wordId);
    }

    public function markLearned(int $userId, int $wordId)
    {
        return $this->repo->updateStatus($userId, $wordId, 'learned');
    }

    public function getUserWords(int $userId, ?string $status = null)
    {
        return $this->repo->getUserWords($userId, $status);
    }
}
