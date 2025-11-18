<?php

namespace App\Repositories\Interfaces;

use App\Models\Word;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface for Word repository operations.
 */
interface WordRepositoryInterface
{
    /**
     * Filter words based on criteria with pagination.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function filter(array $filters, int $perPage = 20): LengthAwarePaginator;

    /**
     * Get a random word from filtered results.
     *
     * @param array<string, mixed> $filters
     * @return Word|null
     */
    public function getRandomWord(array $filters = []): ?Word;

    /**
     * Get a random word excluding specific IDs.
     *
     * @param array<int> $excludeIds
     * @return Word|null
     */
    public function getRandomWordExcluding(array $excludeIds): ?Word;

    /**
     * Get random words from a specific set of IDs.
     *
     * @param array<int> $wordIds
     * @param int $count
     * @return Collection<int, Word>
     */
    public function getRandomWordsFromIds(array $wordIds, int $count = 1): Collection;

    /**
     * Get all available topics.
     *
     * @return \Illuminate\Support\Collection<int, string>
     */
    public function getTopics(): \Illuminate\Support\Collection;

    /**
     * Get all CEFR levels.
     *
     * @return array<string>
     */
    public function getCefrLevels(): array;

    /**
     * Find word by ID.
     */
    public function findById(int $id): ?Word;

    /**
     * Search words by text in word, definition, or example.
     *
     * @param string $searchTerm
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchWords(string $searchTerm, int $perPage = 20): LengthAwarePaginator;

    /**
     * Legacy search method for backward compatibility.
     *
     * @param string $query
     * @param int $limit
     * @return mixed
     */
    public function search(string $query, int $limit = 20);

    /**
     * Get all words with limit.
     *
     * @param int $limit
     * @return mixed
     */
    public function all(int $limit = 50);

    /**
     * Get words that the user hasn't seen before.
     *
     * @param int $userId
     * @param array<string, mixed> $filters
     * @param int $limit
     * @return Collection<int, Word>
     */
    public function getNewWordsForUser(int $userId, array $filters = [], int $limit = 10): Collection;

    /**
     * Get words for review (words with mistakes that aren't mastered).
     *
     * @param int $userId
     * @param int $limit
     * @return Collection<int, Word>
     */
    public function getReviewWordsForUser(int $userId, int $limit = 10): Collection;

    /**
     * Get unmastered words that the user has seen before.
     *
     * @param int $userId
     * @param int $limit
     * @return Collection<int, Word>
     */
    public function getUnmasteredWordsForUser(int $userId, int $limit = 10): Collection;
}
