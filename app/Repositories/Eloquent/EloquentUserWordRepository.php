<?php 

namespace App\Repositories\Eloquent;

use App\Models\UserWord;
use App\Repositories\Interfaces\UserWordRepositoryInterface;

class EloquentUserWordRepository implements UserWordRepositoryInterface
{
    public function addWordToUser(int $userId, int $wordId, string $status = 'saved'): UserWord
    {
        return UserWord::updateOrCreate(
            ['user_id' => $userId, 'word_id' => $wordId],
            ['status' => $status]
        );
    }

    public function removeWord(int $userId, int $wordId): bool
    {
        return UserWord::where('user_id', $userId)
            ->where('word_id', $wordId)
            ->delete() > 0;
    }

    public function updateStatus(int $userId, int $wordId, string $status): bool
    {
        return UserWord::where('user_id', $userId)
            ->where('word_id', $wordId)
            ->update(['status' => $status]) > 0;
    }

    public function getUserWords(int $userId, ?string $status = null)
    {
        $query = UserWord::with('word')->where('user_id', $userId);
        if ($status) $query->where('status', $status);
        return $query->get();
    }
}
