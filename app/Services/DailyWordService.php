<?php

namespace App\Services;

use App\Models\DailyWordHistory;
use App\Repositories\Interfaces\WordRepositoryInterface;

class DailyWordService
{
    public function __construct(
        protected WordRepositoryInterface $wordRepo
    ) {}

    public function getTodayWord()
    {
        $today = now()->toDateString();

        $record = DailyWordHistory::with('word')->where('date', $today)->first();

        if ($record) return $record->word;

        $word = $this->wordRepo->getRandomWord();

        DailyWordHistory::create([
            'word_id' => $word->id,
            'date' => $today,
        ]);

        return $word;
    }
}
