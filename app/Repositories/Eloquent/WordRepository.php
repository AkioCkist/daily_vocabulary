<?php

namespace App\Repositories\Eloquent;

use App\Models\Word;
use App\Repositories\Interfaces\WordRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Eloquent implementation of Word repository.
 */
class WordRepository implements WordRepositoryInterface
{
    /**
     * Filter words based on criteria with pagination.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function filter(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = Word::filter($filters);
        
        // If there's a word search, check for exact match first
        if (!empty($filters['word_search'])) {
            $exactMatch = Word::where('word', '=', $filters['word_search'])->first();
            if ($exactMatch) {
                // Return only the exact match
                return new \Illuminate\Pagination\LengthAwarePaginator(
                    collect([$exactMatch]),
                    1,
                    $perPage,
                    1
                );
            }
        }
        
        return $query->orderBy('word')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get a random word from filtered results.
     *
     * @param array<string, mixed> $filters
     * @return Word|null
     */
    public function getRandomWord(array $filters = []): ?Word
    {
        return Word::filter($filters)->inRandomOrder()->first();
    }

    /**
     * Get a random word excluding specific IDs.
     *
     * @param array<int> $excludeIds
     * @return Word|null
     */
    public function getRandomWordExcluding(array $excludeIds): ?Word
    {
        return Word::whereNotIn('id', $excludeIds)
            ->inRandomOrder()
            ->first();
    }

    /**
     * Get random words from a specific set of IDs.
     *
     * @param array<int> $wordIds
     * @param int $count
     * @return Collection<int, Word>
     */
    public function getRandomWordsFromIds(array $wordIds, int $count = 1): Collection
    {
        return Word::whereIn('id', $wordIds)
            ->inRandomOrder()
            ->limit($count)
            ->get();
    }

    /**
     * Get all available topics.
     *
     * @return \Illuminate\Support\Collection<int, string>
     */
    public function getTopics(): \Illuminate\Support\Collection
    {
        return Word::getTopics();
    }

    /**
     * Get all CEFR levels.
     *
     * @return array<string>
     */
    public function getCefrLevels(): array
    {
        return Word::getCefrLevels();
    }

    /**
     * Find word by ID.
     */
    public function findById(int $id): ?Word
    {
        return Word::find($id);
    }

    /**
     * Search words by text in word, definition, or example.
     * OPTIMIZED: Uses PostgreSQL full-text search (tsvector) instead of LIKE queries.
     * This enables fast searching with indexes instead of full table scans.
     *
     * @param string $searchTerm
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchWords(string $searchTerm, int $perPage = 20): LengthAwarePaginator
    {
        // Convert search term to PostgreSQL tsquery format
        $query = Word::whereRaw(
            'search_vector @@ plainto_tsquery(\'english\', ?)',
            [$searchTerm]
        )
        ->orderByRaw(
            'ts_rank(search_vector, plainto_tsquery(\'english\', ?)) DESC',
            [$searchTerm]
        )
        ->orderBy('word');

        return $query->paginate($perPage);
    }

    /**
     * Legacy search method for backward compatibility.
     * OPTIMIZED: Uses PostgreSQL full-text search (tsvector) instead of LIKE queries.
     *
     * @param string $query
     * @param int $limit
     * @return mixed
     */
    public function search(string $query, int $limit = 20)
    {
        return Word::whereRaw(
            'search_vector @@ plainto_tsquery(\'english\', ?)',
            [$query]
        )
        ->orderByRaw(
            'ts_rank(search_vector, plainto_tsquery(\'english\', ?)) DESC',
            [$query]
        )
        ->limit($limit)
        ->get();
    }

    /**
     * Get all words with limit.
     *
     * @param int $limit
     * @return mixed
     */
    public function all(int $limit = 50)
    {
        return Word::limit($limit)->get();
    }

    /**
     * Get words that the user hasn't seen before.
     *
     * @param int $userId
     * @param array<string, mixed> $filters
     * @param int $limit
     * @return Collection<int, Word>
     */
    public function getNewWordsForUser(int $userId, array $filters = [], int $limit = 10): Collection
    {
        return Word::filter($filters)
            ->whereDoesntHave('users', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('definition', 'not like', '%Auto-generated%')
            ->where('definition', 'not like', '%dolor%')
            ->where('definition', 'not like', '%Lorem%')
            ->where('word', 'not like', 'word_%')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Get words for review (words with mistakes that aren't mastered).
     *
     * @param int $userId
     * @param int $limit
     * @return Collection<int, Word>
     */
    public function getReviewWordsForUser(int $userId, int $limit = 10): Collection
    {
        return Word::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->where('mistake_count', '>', 0)
                ->where('mastered', false);
        })
        ->where('definition', 'not like', '%Auto-generated%')
        ->where('definition', 'not like', '%dolor%')
        ->where('definition', 'not like', '%Lorem%')
        ->where('word', 'not like', 'word_%')
        ->inRandomOrder()
        ->limit($limit)
        ->get();
    }

    /**
     * Get unmastered words that the user has seen before.
     *
     * @param int $userId
     * @param int $limit
     * @return Collection<int, Word>
     */
    public function getUnmasteredWordsForUser(int $userId, int $limit = 10): Collection
    {
        return Word::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->where('is_learned', false)
                ->where('mastered', false)
                ->whereNotNull('last_seen_at');
        })
        ->where('definition', 'not like', '%Auto-generated%')
        ->where('definition', 'not like', '%dolor%')
        ->where('definition', 'not like', '%Lorem%')
        ->where('word', 'not like', 'word_%')
        ->inRandomOrder()
        ->limit($limit)
        ->get();
    }

    /**
     * Get all words from user's vocabulary list.
     *
     * @param int $userId
     * @param int $limit
     * @return Collection<int, Word>
     */
    public function getUserVocabularyWords(int $userId, int $limit = 20): Collection
    {
        return Word::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('definition', 'not like', '%Auto-generated%')
        ->where('definition', 'not like', '%dolor%')
        ->where('definition', 'not like', '%Lorem%')
        ->where('word', 'not like', 'word_%')
        ->inRandomOrder()
        ->limit($limit)
        ->get();
    }
}