<?php

namespace App\Services;

use App\Models\Topic;
use App\Models\User;
use App\Models\Word;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * Service for managing topics (both system and user-created).
 */
class TopicService
{
    /**
     * Get all system topics.
     *
     * @return Collection<int, Topic>
     */
    public function getSystemTopics(): Collection
    {
        return Topic::where('is_system', true)
            ->withCount('words')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get user's custom topics.
     *
     * @param User $user
     * @return Collection<int, Topic>
     */
    public function getUserTopics(User $user): Collection
    {
        return Topic::where('user_id', $user->id)
            ->withCount('words')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all available topics for a user (system + user's custom topics).
     *
     * @param User $user
     * @return array<string, Collection>
     */
    public function getAllAvailableTopics(User $user): array
    {
        return [
            'system' => $this->getSystemTopics(),
            'user' => $this->getUserTopics($user),
        ];
    }

    /**
     * Create a new custom topic for a user.
     *
     * @param User $user
     * @param array<string, mixed> $data
     * @return Topic
     * @throws ValidationException
     */
    public function createUserTopic(User $user, array $data): Topic
    {
        // Check if topic name already exists for this user
        if ($this->topicExistsForUser($user, $data['name'])) {
            throw ValidationException::withMessages([
                'name' => ['A topic with this name already exists.']
            ]);
        }

        return Topic::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'user_id' => $user->id,
            'is_system' => false,
        ]);
    }

    /**
     * Update a user's custom topic.
     *
     * @param User $user
     * @param int $topicId
     * @param array<string, mixed> $data
     * @return Topic
     * @throws ValidationException
     */
    public function updateUserTopic(User $user, int $topicId, array $data): Topic
    {
        $topic = Topic::where('id', $topicId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Check if new name conflicts with existing topics
        if (isset($data['name']) && $data['name'] !== $topic->name) {
            if ($this->topicExistsForUser($user, $data['name'])) {
                throw ValidationException::withMessages([
                    'name' => ['A topic with this name already exists.']
                ]);
            }
        }

        $topic->update([
            'name' => $data['name'] ?? $topic->name,
            'description' => $data['description'] ?? $topic->description,
        ]);

        return $topic->fresh();
    }

    /**
     * Delete a user's custom topic.
     *
     * @param User $user
     * @param int $topicId
     * @return bool
     */
    public function deleteUserTopic(User $user, int $topicId): bool
    {
        $topic = Topic::where('id', $topicId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Check if topic has associated words
        if ($topic->words()->count() > 0) {
            throw ValidationException::withMessages([
                'topic' => ['Cannot delete topic that has associated words.']
            ]);
        }

        return $topic->delete();
    }

    /**
     * Get topics with word counts for filtering purposes.
     *
     * @param User $user
     * @return array<string, mixed>
     */
    public function getTopicsForFiltering(User $user): array
    {
        $systemTopics = $this->getSystemTopics()
            ->map(function ($topic) {
                return [
                    'id' => $topic->id,
                    'name' => $topic->name,
                    'description' => $topic->description,
                    'word_count' => $topic->words_count,
                    'type' => 'system',
                ];
            });

        $userTopics = $this->getUserTopics($user)
            ->map(function ($topic) {
                return [
                    'id' => $topic->id,
                    'name' => $topic->name,
                    'description' => $topic->description,
                    'word_count' => $topic->words_count,
                    'type' => 'user',
                ];
            });

        return [
            'system' => $systemTopics->toArray(),
            'user' => $userTopics->toArray(),
            'all' => $systemTopics->concat($userTopics)->toArray(),
        ];
    }

    /**
     * Get suggested system topics (most popular ones).
     *
     * @return Collection<int, Topic>
     */
    public function getSuggestedTopics(): Collection
    {
        return Topic::where('is_system', true)
            ->withCount('words')
            ->orderBy('words_count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Search topics by name.
     *
     * @param User $user
     * @param string $query
     * @return Collection<int, Topic>
     */
    public function searchTopics(User $user, string $query): Collection
    {
        return Topic::where(function ($q) use ($user, $query) {
                $q->where('is_system', true)
                  ->orWhere('user_id', $user->id);
            })
            ->where('name', 'like', "%{$query}%")
            ->withCount('words')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get words by topic for flashcard generation.
     *
     * @param array<int> $topicIds
     * @param string|null $cefrLevel
     * @param int $limit
     * @return Collection<int, Word>
     */
    public function getWordsByTopics(array $topicIds, ?string $cefrLevel = null, int $limit = 10): Collection
    {
        $topicNames = Topic::whereIn('id', $topicIds)->pluck('name');
        $query = Word::whereIn('topic', $topicNames);

        if ($cefrLevel) {
            $query->where('cefr_level', $cefrLevel);
        }

        return $query->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Check if a topic name already exists for a user.
     *
     * @param User $user
     * @param string $name
     * @return bool
     */
    private function topicExistsForUser(User $user, string $name): bool
    {
        return Topic::where(function ($query) use ($user, $name) {
            $query->where('is_system', true)
                  ->orWhere('user_id', $user->id);
        })
        ->where('name', $name)
        ->exists();
    }

    /**
     * Initialize system topics if they don't exist.
     *
     * @return void
     */
    public function initializeSystemTopics(): void
    {
        $systemTopics = [
            ['name' => 'Business', 'description' => 'Business and professional vocabulary'],
            ['name' => 'Technology', 'description' => 'Technology and computing terms'],
            ['name' => 'Travel', 'description' => 'Travel and tourism vocabulary'],
            ['name' => 'Health', 'description' => 'Health and medical terms'],
            ['name' => 'Education', 'description' => 'Educational and academic vocabulary'],
            ['name' => 'Food & Dining', 'description' => 'Food, cooking, and dining vocabulary'],
            ['name' => 'Sports', 'description' => 'Sports and fitness terminology'],
            ['name' => 'Entertainment', 'description' => 'Movies, music, and entertainment'],
            ['name' => 'Science', 'description' => 'Scientific and research vocabulary'],
            ['name' => 'Daily Life', 'description' => 'Everyday activities and common situations'],
        ];

        foreach ($systemTopics as $topicData) {
            Topic::firstOrCreate(
                ['name' => $topicData['name'], 'is_system' => true],
                [
                    'description' => $topicData['description'],
                    'user_id' => null,
                    'is_system' => true,
                ]
            );
        }
    }
}