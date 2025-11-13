<?php

namespace App\Services;

use App\Models\DailyWordHistory;
use App\Models\UserWord;
use App\Repositories\Interfaces\WordRepositoryInterface;

class DailyWordService
{
    public function __construct(
        protected WordRepositoryInterface $wordRepo
    ) {}

    public function getTodayWord(?int $userId = null)
    {
        $today = now()->toDateString();

        $record = DailyWordHistory::with('word')->where('date', $today)->first();

        // If no word of the day exists yet, create one
        if (!$record) {
            $word = $this->wordRepo->getRandomWord();

            DailyWordHistory::create([
                'word_id' => $word->id,
                'date' => $today,
            ]);

            return $word;
        }

        // If user is not logged in, just return the word of the day
        if (!$userId) {
            return $record->word;
        }

        // Check if user already has this word in their vocabulary
        $userHasWord = UserWord::where('user_id', $userId)
            ->where('word_id', $record->word->id)
            ->exists();

        // If user doesn't have the word, return it
        if (!$userHasWord) {
            return $record->word;
        }

        // If user already has the word, get a different random word they don't have
        $userWordIds = UserWord::where('user_id', $userId)
            ->pluck('word_id')
            ->toArray();

        $newWord = $this->wordRepo->getRandomWordExcluding($userWordIds);

        return $newWord ?? $record->word; // Fallback to word of the day if no new words available
    }
}
