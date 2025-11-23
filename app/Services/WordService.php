<?php

namespace App\Services;

use App\Models\Word;
use App\Repositories\Interfaces\WordRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service for handling word-related operations.
 */
class WordService
{
    public function __construct(
        private WordRepositoryInterface $wordRepository
    ) {}

    /**
     * Filter words based on criteria with pagination.
     *
     * @param array<string, mixed> $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function filterWords(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->wordRepository->filter($filters, $perPage);
    }

    /**
     * Get a random word from filtered results.
     *
     * @param array<string, mixed> $filters
     * @return Word|null
     */
    public function getRandomWord(array $filters = []): ?Word
    {
        return $this->wordRepository->getRandomWord($filters);
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
        return $this->wordRepository->getRandomWordsFromIds($wordIds, $count);
    }

    /**
     * Get all available topics with Redis caching.
     *
     * @return \Illuminate\Support\Collection<int, string>
     */
    public function getTopics(): \Illuminate\Support\Collection
    {
        return \Illuminate\Support\Facades\Cache::remember(
            'vocabulary:topics:list',
            now()->addDays(7), // Cache for 7 days
            fn() => $this->wordRepository->getTopics()
        );
    }

    /**
     * Get all CEFR levels with Redis caching.
     *
     * @return array<string>
     */
    public function getCefrLevels(): array
    {
        return \Illuminate\Support\Facades\Cache::remember(
            'vocabulary:cefr:levels',
            now()->addDays(7), // Cache for 7 days
            fn() => $this->wordRepository->getCefrLevels()
        );
    }

    /**
     * Find word by ID.
     */
    public function findById(int $id): ?Word
    {
        return $this->wordRepository->findById($id);
    }

    /**
     * Search words by text in word, definition, or example.
     *
     * @param string $searchTerm
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function searchWords(string $searchTerm, int $perPage = 20): LengthAwarePaginator
    {
        return $this->wordRepository->searchWords($searchTerm, $perPage);
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
        return $this->wordRepository->getNewWordsForUser($userId, $filters, $limit);
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
        return $this->wordRepository->getReviewWordsForUser($userId, $limit);
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
        return $this->wordRepository->getUnmasteredWordsForUser($userId, $limit);
    }
}