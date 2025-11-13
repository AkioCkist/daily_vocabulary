<?php

namespace App\Repositories\Eloquent;

use App\Models\Word;
use App\Repositories\Interfaces\WordRepositoryInterface;

class EloquentWordRepository implements WordRepositoryInterface
{
    public function getRandomWord(): ?Word
    {
        return Word::inRandomOrder()->first();
    }

    public function findById(int $id): ?Word
    {
        return Word::find($id);
    }

    public function search(string $query, int $limit = 20)
    {
        return Word::where('word', 'like', "%{$query}%")
            ->orWhere('definition', 'like', "%{$query}%")
            ->limit($limit)
            ->get();
    }

    public function all(int $limit = 50)
    {
        return Word::limit($limit)->get();
    }
}
