<?php 

namespace App\Repositories\Interfaces;

use App\Models\UserWord;

interface UserWordRepositoryInterface
{
    public function addWordToUser(int $userId, int $wordId, string $status = 'saved'): UserWord;
    public function removeWord(int $userId, int $wordId): bool;
    public function updateStatus(int $userId, int $wordId, string $status): bool;
    public function getUserWords(int $userId, ?string $status = null);
}
