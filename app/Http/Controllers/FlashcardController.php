<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FlashcardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {
    }

    /**
     * Start a flashcard session.
     */
    public function start(Request $request): Response|RedirectResponse
    {
        // Debug the incoming request
        Log::info('Flashcard start request received:', [
            'all_data' => $request->all(),
            'mode' => $request->get('mode'),
            'flashcard_type' => $request->get('flashcard_type'),
            'word_count' => $request->get('word_count'),
        ]);

        $validation = [
            'mode' => 'required|in:basic,advanced,topic,quick,review,saved_session',
            'flashcard_type' => 'required|in:standard,fill_blank,mixed',
            'cefr_levels' => 'nullable|array',
            'cefr_levels.*' => 'string|in:A1,A2,B1,B2,C1,C2',
            'topic_ids' => 'nullable|array',
            'topic_ids.*' => 'integer|exists:topics,id',
            'saved_session_id' => 'nullable|integer|exists:saved_sessions,id',
            'flashcard_ids' => 'nullable|array',
            'flashcard_ids.*' => 'integer',
            'shuffle' => 'nullable|boolean',
        ];

        // For review mode, allow smaller word counts (minimum 1)
        if ($request->get('mode') === 'review') {
            $validation['word_count'] = 'required|integer|min:1|max:50';
        } else {
            $validation['word_count'] = 'required|integer|min:5|max:50';
        }

        try {
            $request->validate($validation);
            Log::info('Flashcard validation passed');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Flashcard validation failed:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        }

        $user = Auth::user();

        // Generate flashcards based on settings
        $words = $this->generateFlashcards($request->all());

        // Debug the words
        Log::info('Generated words for flashcards:', ['count' => count($words), 'words' => $words]);

        // Check if we have words available
        if (empty($words)) {
            return back()->with('error', 'No words available for flashcard practice. Please add some words first.');
        }

        // Store session in session storage
        session([
            'flashcard_session' => [
                'words' => $words,
                'current_index' => 0,
                'settings' => $request->all(),
                'started_at' => now(),
            ]
        ]);

        // Get user's custom topics for adding words
        $userTopics = \App\Models\Topic::where('user_id', $user->id)
            ->select(['id', 'name', 'description'])
            ->orderBy('name')
            ->get();

        // Redirect to GET route for practice page
        return redirect()->route('flashcards.practice');
    }

    /**
     * Get the next flashcard in the session.
     */
    public function next(Request $request): JsonResponse
    {
        $session = session('flashcard_session');

        if (!$session) {
            return response()->json(['error' => 'No active session'], 404);
        }

        $currentIndex = $session['current_index'];
        $words = $session['words'];

        if ($currentIndex >= count($words)) {
            return response()->json(['completed' => true]);
        }

        $word = $words[$currentIndex];

        // Update session index
        session(['flashcard_session.current_index' => $currentIndex + 1]);

        return response()->json([
            'word' => $word,
            'progress' => [
                'current' => $currentIndex + 1,
                'total' => count($words)
            ]
        ]);
    }

    /**
     * Submit an answer for the current flashcard.
     */
    public function answer(Request $request)
    {
        $request->validate([
            'word_id' => 'required|integer|exists:words,id',
            'is_correct' => 'nullable|boolean',
            'user_answer' => 'nullable|string',
            'forgotten' => 'nullable|boolean',
            'hints_used' => 'nullable|integer|min:0',
            'response_time' => 'nullable|integer|min:0',
            'flashcard_type' => 'required|in:standard,fill_blank',
        ]);

        $user = Auth::user();
        $session = session('flashcard_session');

        if (!$session) {
            return response()->json(['error' => 'No active session'], 404);
        }

        $wordId = $request->get('word_id');
        $isCorrect = $request->get('is_correct');
        $userAnswer = $request->get('user_answer');
        $forgotten = $request->get('forgotten', false);
        $hintsUsed = $request->get('hints_used', 0);
        $flashcardType = $request->get('flashcard_type');

        // Handle "I don't remember" action
        if ($forgotten) {
            $isCorrect = false;
            $userAnswer = '[FORGOTTEN]';

            // Update forgotten count in user_words
            $userWord = \App\Models\UserWord::firstOrCreate(
                ['user_id' => $user->id, 'word_id' => $wordId],
                ['mastered' => false, 'mistake_count' => 0]
            );

            $userWord->increment('forgotten_count');
            $userWord->increment('mistake_count');

            if ($hintsUsed > 0) {
                $userWord->increment('hint_reveals_used', $hintsUsed);
            }
        }

        // For fill-in-the-blank, evaluate the answer
        if ($flashcardType === 'fill_blank' && !$forgotten) {
            $word = \App\Models\Word::find($wordId);
            $correctAnswer = strtolower(trim($word->word));
            $submittedAnswer = strtolower(trim($userAnswer ?? ''));

            $isCorrect = $correctAnswer === $submittedAnswer;

            // Update fill_blank_attempts count
            $userWord = \App\Models\UserWord::firstOrCreate(
                ['user_id' => $user->id, 'word_id' => $wordId],
                ['mastered' => false, 'mistake_count' => 0]
            );

            $userWord->increment('fill_blank_attempts');

            if ($hintsUsed > 0) {
                $userWord->increment('hint_reveals_used', $hintsUsed);
            }
        }

        // Update user progress
        $userProgressService = app(\App\Services\UserProgressService::class);
        $userProgressService->updateWordProgress($user, $wordId, $isCorrect);

        // Record detailed flashcard attempt
        \App\Models\FlashcardAttempt::create([
            'user_id' => $user->id,
            'word_id' => $wordId,
            'mode' => $flashcardType,
            'is_correct' => $isCorrect,
            'user_answer' => $userAnswer,
            'hints_used' => $hintsUsed,
            'was_forgotten' => $forgotten,
            'response_time_ms' => $request->get('response_time'),
            'hint_progression' => $request->get('hint_progression', []),
        ]);

        // Also record in test_attempts for compatibility
        \App\Models\TestAttempt::create([
            'user_id' => $user->id,
            'word_id' => $wordId,
            'is_correct' => $isCorrect,
            'answer_text' => $userAnswer ?? ($isCorrect ? 'Flashcard - Correct' : 'Flashcard - Incorrect'),
            'time_taken' => $request->get('response_time'),
        ]);

        // Calculate difficulty adjustment
        $this->updateDifficultyScore($user->id, $wordId, $isCorrect, $hintsUsed, $forgotten);

        return response()->json([
            'success' => true,
            'is_correct' => $isCorrect,
            'hints_used' => $hintsUsed,
        ]);
    }

    /**
     * Get a hint for the current word in fill-in-the-blank mode.
     */
    public function getHint(Request $request): JsonResponse
    {
        $request->validate([
            'word_id' => 'required|integer|exists:words,id',
            'current_hint_level' => 'required|integer|min:0',
        ]);

        $word = \App\Models\Word::find($request->get('word_id'));
        $hintLevel = $request->get('current_hint_level');
        $targetWord = $word->word;

        // Calculate how many characters to reveal (progressive hints)
        $wordLength = strlen($targetWord);
        $charactersToReveal = min($hintLevel + 1, $wordLength);

        // Create hint by revealing characters and hiding the rest
        $hint = substr($targetWord, 0, $charactersToReveal) . str_repeat('_', $wordLength - $charactersToReveal);

        // Check if this is the complete word (no more hints available)
        $maxHintsReached = $charactersToReveal >= $wordLength;

        return response()->json([
            'hint' => $hint,
            'hint_level' => $hintLevel + 1,
            'max_hints_reached' => $maxHintsReached,
            'characters_revealed' => $charactersToReveal,
            'total_characters' => $wordLength,
        ]);
    }

    /**
     * Update difficulty score based on performance.
     */
    private function updateDifficultyScore(int $userId, int $wordId, bool $isCorrect, int $hintsUsed, bool $forgotten): void
    {
        $userWord = \App\Models\UserWord::firstOrCreate(
            ['user_id' => $userId, 'word_id' => $wordId],
            ['mastered' => false, 'mistake_count' => 0, 'difficulty_score' => 0.5]
        );

        $currentScore = $userWord->difficulty_score ?? 0.5;

        // Calculate new difficulty score (0.0 = very easy, 1.0 = very hard)
        if ($forgotten) {
            $newScore = min(1.0, $currentScore + 0.3); // Forgotten = significant difficulty increase
        } elseif (!$isCorrect) {
            $newScore = min(1.0, $currentScore + 0.2); // Incorrect = moderate difficulty increase
        } elseif ($hintsUsed > 0) {
            // Correct with hints = slight difficulty increase based on hints used
            $hintPenalty = $hintsUsed * 0.05;
            $newScore = min(1.0, $currentScore + $hintPenalty);
        } else {
            $newScore = max(0.0, $currentScore - 0.1); // Correct without hints = difficulty decrease
        }

        $userWord->update(['difficulty_score' => round($newScore, 2)]);
    }

    /**
     * Complete the flashcard session.
     */
    public function complete()
    {
        Log::info('FlashcardController::complete - Method called', [
            'request_method' => request()->method(),
            'is_inertia' => !!request()->header('X-Inertia'),
            'inertia_headers' => [
                'X-Inertia' => request()->header('X-Inertia'),
                'X-Inertia-Partial-Data' => request()->header('X-Inertia-Partial-Data'),
                'X-Inertia-Partial-Component' => request()->header('X-Inertia-Partial-Component'),
            ]
        ]);

        $session = session('flashcard_session');

        Log::info('FlashcardController::complete - Session check', [
            'has_session' => !!$session,
            'session_keys' => $session ? array_keys($session) : [],
        ]);

        if (!$session) {
            Log::warning('FlashcardController::complete - No active session found');
            if (request()->header('X-Inertia')) {
                return response()->noContent();
            }
            return redirect()->route('home')->with('error', 'No active session found');
        }

        // Calculate session statistics
        $stats = [
            'total_words' => count($session['words']),
            'duration' => now()->diffInSeconds($session['started_at']),
            'completed_at' => now(),
        ];

        // Debug session data
        Log::info('Flashcard session complete - Debug:', [
            'has_session' => !!$session,
            'session_settings' => $session['settings'] ?? null,
            'has_saved_session_id' => isset($session['settings']['saved_session_id']),
            'saved_session_id' => $session['settings']['saved_session_id'] ?? null,
            'words_count' => isset($session['words']) ? count($session['words']) : 0,
            'mode' => $session['settings']['mode'] ?? null,
        ]);

        // Prepare save session data for popup (only for study sessions, not review)
        $saveSessionData = null;
        $shouldShowSavePopup = false;

        // Check if this is a study session (not a review of saved session)
        if (
            !isset($session['settings']['saved_session_id']) &&
            isset($session['words']) &&
            count($session['words']) > 0
        ) {

            $shouldShowSavePopup = true;

            // Extract flashcard IDs from session
            $flashcardIds = [];
            foreach ($session['words'] as $word) {
                if (isset($word['id'])) {
                    $flashcardIds[] = $word['id'];
                }
            }

            $saveSessionData = [
                'flashcard_ids' => $flashcardIds,
                'suggested_name' => \App\Models\SavedSession::generateSessionName(
                    $session['settings']['topic'] ?? null
                ),
                'topic' => $session['settings']['topic'] ?? null,
                'total_words' => count($session['words']),
            ];

            Log::info('Should show save popup:', [
                'should_show' => $shouldShowSavePopup,
                'flashcard_ids_count' => count($flashcardIds),
                'suggested_name' => $saveSessionData['suggested_name'],
                'topic' => $saveSessionData['topic'],
            ]);
        } else {
            Log::info('Not showing save popup because:', [
                'has_saved_session_id' => isset($session['settings']['saved_session_id']),
                'has_words' => isset($session['words']),
                'words_count' => isset($session['words']) ? count($session['words']) : 0,
            ]);
        }

        // Clear session
        session()->forget('flashcard_session');
        Log::info('FlashcardController::complete - Session cleared');

        // If it's an Inertia request with preserveState, return data for popup
        if (request()->header('X-Inertia') && request()->header('X-Inertia-Partial-Data')) {
            Log::info('FlashcardController::complete - Returning JSON response for Inertia partial');
            return response()->json([
                'stats' => $stats,
                'show_save_popup' => $shouldShowSavePopup,
                'save_session_data' => $saveSessionData,
            ]);
        }

        // For regular requests, redirect with data
        $url = route('home');

        Log::info('FlashcardController::complete - Preparing redirect', [
            'base_url' => $url,
            'should_show_save_popup' => $shouldShowSavePopup,
            'is_inertia_request' => !!request()->header('X-Inertia'),
        ]);

        if ($shouldShowSavePopup) {
            // Add query parameters for save session popup
            $queryParams = [
                'show_save_popup' => 'true',
                'save_session_data' => urlencode(json_encode($saveSessionData))
            ];
            $url .= '?' . http_build_query($queryParams);

            Log::info('FlashcardController::complete - Built URL with save popup params', [
                'final_url' => $url,
                'query_params' => $queryParams,
                'save_session_data' => $saveSessionData,
            ]);
        }

        Log::info('FlashcardController::complete - Redirecting', [
            'final_url' => $url,
            'with_data' => [
                'message' => 'Session completed successfully',
                'flashcard_stats' => $stats
            ]
        ]);

        return redirect($url)->with([
            'message' => 'Session completed successfully',
            'flashcard_stats' => $stats
        ]);
    }

    /**
     * Add a word to user's personal topic collection.
     */
    public function addToTopic(Request $request): JsonResponse
    {
        $request->validate([
            'word_id' => 'required|integer|exists:words,id',
            'topic_id' => 'required|integer|exists:topics,id',
        ]);

        $user = Auth::user();
        $wordId = $request->get('word_id');
        $topicId = $request->get('topic_id');

        // Verify the topic belongs to the user
        $topic = \App\Models\Topic::where('id', $topicId)
            ->where('user_id', $user->id)
            ->first();

        if (!$topic) {
            return response()->json([
                'success' => false,
                'message' => 'Topic not found or does not belong to you.'
            ], 403);
        }

        // Check if already added
        $exists = DB::table('user_word_topics')
            ->where('user_id', $user->id)
            ->where('word_id', $wordId)
            ->where('topic_id', $topicId)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Word is already in this topic.'
            ], 409);
        }

        // Add to topic
        DB::table('user_word_topics')->insert([
            'user_id' => $user->id,
            'word_id' => $wordId,
            'topic_id' => $topicId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Word added to topic successfully.'
        ]);
    }

    /**
     * Remove a word from user's personal topic collection.
     */
    public function removeFromTopic(Request $request): JsonResponse
    {
        $request->validate([
            'word_id' => 'required|integer|exists:words,id',
            'topic_id' => 'required|integer|exists:topics,id',
        ]);

        $user = Auth::user();
        $wordId = $request->get('word_id');
        $topicId = $request->get('topic_id');

        // Verify the topic belongs to the user
        $topic = \App\Models\Topic::where('id', $topicId)
            ->where('user_id', $user->id)
            ->first();

        if (!$topic) {
            return response()->json([
                'success' => false,
                'message' => 'Topic not found or does not belong to you.'
            ], 403);
        }

        // Remove from topic
        $deleted = DB::table('user_word_topics')
            ->where('user_id', $user->id)
            ->where('word_id', $wordId)
            ->where('topic_id', $topicId)
            ->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Word not found in this topic.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Word removed from topic successfully.'
        ]);
    }

    /**
     * Quick create a new topic during flashcard setup.
     */
    public function quickCreateTopic(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        // Check if topic with same name already exists (globally, due to unique constraint)
        $existingTopic = \App\Models\Topic::where('name', $request->get('name'))
            ->first();

        if ($existingTopic) {
            return response()->json([
                'success' => false,
                'message' => 'A topic with this name already exists. Please choose a different name.'
            ], 409);
        }

        // Create the topic
        $topic = \App\Models\Topic::create([
            'user_id' => $user->id,
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'is_system' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Topic created successfully.',
            'topic' => [
                'id' => $topic->id,
                'name' => $topic->name,
                'description' => $topic->description,
                'words_count' => 0,
            ]
        ]);
    }

    /**
     * Delete a user's topic.
     */
    public function deleteTopic(Request $request, int $topicId): JsonResponse
    {
        $user = Auth::user();

        // Find the topic and verify ownership
        $topic = \App\Models\Topic::where('id', $topicId)
            ->where('user_id', $user->id)
            ->where('is_system', false) // Prevent deletion of system topics
            ->first();

        if (!$topic) {
            return response()->json([
                'success' => false,
                'message' => 'Topic not found or cannot be deleted.'
            ], 404);
        }

        // Delete associated word relationships
        DB::table('user_word_topics')
            ->where('topic_id', $topicId)
            ->where('user_id', $user->id)
            ->delete();

        // Delete the topic
        $topic->delete();

        return response()->json([
            'success' => true,
            'message' => 'Topic deleted successfully.'
        ]);
    }

    /**
     * Get user's topics for a specific word (to show which topics it's in).
     */
    public function getWordTopics(Request $request, int $wordId): JsonResponse
    {
        $request->validate([]);

        $user = Auth::user();

        // Get all user topics with a flag indicating if word is in them
        $userTopics = \App\Models\Topic::where('user_id', $user->id)
            ->select(['id', 'name', 'description'])
            ->get()
            ->map(function ($topic) use ($user, $wordId) {
                $isAdded = DB::table('user_word_topics')
                    ->where('user_id', $user->id)
                    ->where('word_id', $wordId)
                    ->where('topic_id', $topic->id)
                    ->exists();

                return [
                    'id' => $topic->id,
                    'name' => $topic->name,
                    'description' => $topic->description,
                    'is_added' => $isAdded,
                ];
            });

        return response()->json([
            'success' => true,
            'topics' => $userTopics
        ]);
    }

    /**
     * Generate flashcards based on settings.
     */
    private function generateFlashcards(array $settings): array
    {
        $user = Auth::user();

        // For saved_session mode, get words from flashcard_ids
        if ($settings['mode'] === 'saved_session' && !empty($settings['flashcard_ids'])) {
            $flashcardIds = $settings['flashcard_ids'];

            // If shuffle is enabled, shuffle the IDs
            if (!empty($settings['shuffle'])) {
                shuffle($flashcardIds);
            }

            // Get words in the specified order
            $words = \App\Models\Word::whereIn('id', $flashcardIds)
                ->get(['id', 'word', 'pronunciation', 'definition', 'example', 'cefr_level', 'topic'])
                ->keyBy('id');

            // Maintain the order of flashcard_ids
            $orderedWords = [];
            foreach ($flashcardIds as $id) {
                if (isset($words[$id])) {
                    $orderedWords[] = $words[$id]->toArray();
                }
            }

            return $orderedWords;
        }

        // For review mode, get words that need review (same logic as DashboardService)
        if ($settings['mode'] === 'review') {
            $words = \App\Models\Word::select(['words.id', 'words.word', 'words.pronunciation', 'words.definition', 'words.example', 'words.cefr_level', 'words.topic'])
                ->join('user_words', 'words.id', '=', 'user_words.word_id')
                ->where('user_words.user_id', $user->id)
                ->where('user_words.mistake_count', '>', 0)
                ->where('user_words.mastered', false)
                ->inRandomOrder()
                ->limit($settings['word_count'])
                ->get();
            return $words->toArray();
        }

        $query = \App\Models\Word::query();

        // For quick mode, just get random words
        if ($settings['mode'] === 'quick') {
            $words = $query->inRandomOrder()
                ->limit($settings['word_count'])
                ->get(['id', 'word', 'pronunciation', 'definition', 'example', 'cefr_level', 'topic']);
            return $words->toArray();
        }

        // Apply CEFR level filter for advanced modes
        if (!empty($settings['cefr_levels'])) {
            $query->whereIn('cefr_level', $settings['cefr_levels']);
        }

        // Apply topic filter
        if (!empty($settings['topic_ids'])) {
            // Separate system topics from user topics
            $topics = \App\Models\Topic::whereIn('id', $settings['topic_ids'])->get();
            $systemTopicNames = $topics->where('is_system', true)->pluck('name')->toArray();
            $userTopicIds = $topics->where('is_system', false)->pluck('id')->toArray();

            $query->where(function ($q) use ($systemTopicNames, $userTopicIds, $user) {
                // Include words from system topics (matched by topic name)
                if (!empty($systemTopicNames)) {
                    $q->whereIn('topic', $systemTopicNames);
                }

                // Include words from user's personal topics (from user_word_topics)
                if (!empty($userTopicIds)) {
                    $q->orWhereHas('userTopics', function ($subQuery) use ($userTopicIds, $user) {
                        $subQuery->whereIn('topics.id', $userTopicIds)
                            ->where('user_word_topics.user_id', $user->id);
                    });
                }
            });
        }

        // Apply advanced filters if they exist
        // Join with user_words for advanced filtering
        $needsUserWordsJoin = !empty($settings['difficulty_filter']) ||
            !empty($settings['mastery_filter']) ||
            !empty($settings['time_filter']);

        if ($needsUserWordsJoin) {
            $query->leftJoin('user_words', function ($join) use ($user) {
                $join->on('words.id', '=', 'user_words.word_id')
                    ->where('user_words.user_id', '=', $user->id);
            });
        }

        // Difficulty filter
        if (!empty($settings['difficulty_filter']) && $settings['difficulty_filter'] !== 'all') {
            switch ($settings['difficulty_filter']) {
                case 'easy':
                    $query->where(function ($q) {
                        $q->where('user_words.difficulty_score', '<=', 0.33)
                            ->orWhereNull('user_words.difficulty_score');
                    });
                    break;
                case 'medium':
                    $query->whereBetween('user_words.difficulty_score', [0.34, 0.66]);
                    break;
                case 'hard':
                    $query->where('user_words.difficulty_score', '>=', 0.67);
                    break;
            }
        }

        // Mastery filter
        if (!empty($settings['mastery_filter']) && $settings['mastery_filter'] !== 'all') {
            if ($settings['mastery_filter'] === 'mastered') {
                $query->where('user_words.mastered', true);
            } elseif ($settings['mastery_filter'] === 'not_mastered') {
                $query->where(function ($q) {
                    $q->where('user_words.mastered', false)
                        ->orWhereNull('user_words.mastered');
                });
            }
        }

        // Time-based filter
        if (!empty($settings['time_filter']) && $settings['time_filter'] !== 'all') {
            if ($settings['time_filter'] === 'recent') {
                // Words studied in the last 7 days
                $query->where('user_words.last_reviewed_at', '>=', now()->subDays(7));
            } elseif ($settings['time_filter'] === 'not_recent') {
                // Words not studied in the last 7 days or never studied
                $query->where(function ($q) {
                    $q->where('user_words.last_reviewed_at', '<', now()->subDays(7))
                        ->orWhereNull('user_words.last_reviewed_at');
                });
            }
        }

        // Apply sorting
        $sortBy = $settings['sort_by'] ?? 'random';

        switch ($sortBy) {
            case 'alphabetical':
                $query->orderBy('words.word', 'asc');
                break;
            case 'difficulty':
                if (!$needsUserWordsJoin) {
                    $query->leftJoin('user_words', function ($join) use ($user) {
                        $join->on('words.id', '=', 'user_words.word_id')
                            ->where('user_words.user_id', '=', $user->id);
                    });
                }
                $query->orderByRaw('COALESCE(user_words.difficulty_score, 0.5) DESC');
                break;
            case 'frequency':
                // If you have a frequency column in words table, use it
                // Otherwise, fall back to random
                $query->inRandomOrder();
                break;
            case 'random':
            default:
                $query->inRandomOrder();
                break;
        }

        // Select distinct words to avoid duplicates from joins
        $selectFields = $needsUserWordsJoin || $sortBy === 'difficulty'
            ? ['words.id', 'words.word', 'words.pronunciation', 'words.definition', 'words.example', 'words.cefr_level', 'words.topic']
            : ['id', 'word', 'pronunciation', 'definition', 'example', 'cefr_level', 'topic'];

        // Get words
        $words = $query->distinct()
            ->limit($settings['word_count'])
            ->get($selectFields);

        return $words->toArray();
    }

    /**
     * Save current settings as a template.
     */
    public function saveTemplate(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'settings' => 'required|array',
            'settings.word_count' => 'required|integer|min:5|max:50',
            'settings.flashcard_type' => 'required|in:standard,fill_blank,mixed',
            'settings.cefr_levels' => 'nullable|array',
            'settings.topic_ids' => 'nullable|array',
            'settings.difficulty_filter' => 'nullable|string|in:all,easy,medium,hard',
            'settings.mastery_filter' => 'nullable|string|in:all,mastered,not_mastered',
            'settings.time_filter' => 'nullable|string|in:all,recent,not_recent',
            'settings.sort_by' => 'nullable|string|in:random,alphabetical,difficulty,frequency',
        ]);

        $user = Auth::user();

        // Check if template with same name exists for this user
        $existing = \App\Models\FlashcardTemplate::where('user_id', $user->id)
            ->where('name', $request->get('name'))
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'A template with this name already exists.'
            ], 409);
        }

        $template = \App\Models\FlashcardTemplate::create([
            'user_id' => $user->id,
            'name' => $request->get('name'),
            'settings' => $request->get('settings'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Template saved successfully.',
            'template' => $template
        ]);
    }

    /**
     * List all templates for the current user.
     */
    public function listTemplates(): JsonResponse
    {
        $user = Auth::user();

        $templates = \App\Models\FlashcardTemplate::where('user_id', $user->id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'templates' => $templates
        ]);
    }

    /**
     * Load a specific template.
     */
    public function loadTemplate(int $id): JsonResponse
    {
        $user = Auth::user();

        $template = \App\Models\FlashcardTemplate::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'template' => $template
        ]);
    }

    /**
     * Delete a template.
     */
    public function deleteTemplate(int $id): JsonResponse
    {
        $user = Auth::user();

        $template = \App\Models\FlashcardTemplate::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found.'
            ], 404);
        }

        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template deleted successfully.'
        ]);
    }

    /**
     * Export a template as JSON file.
     */
    public function exportTemplate(int $id)
    {
        $user = Auth::user();

        $template = \App\Models\FlashcardTemplate::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found.'
            ], 404);
        }

        $exportData = [
            'name' => $template->name,
            'settings' => $template->settings,
            'exported_at' => now()->toIso8601String(),
            'version' => '1.0'
        ];

        $filename = 'flashcard_template_' . str_replace(' ', '_', strtolower($template->name)) . '.json';

        return response()->json($exportData)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Import a template from JSON file.
     */
    public function importTemplate(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:json,txt|max:1024', // Max 1MB
        ]);

        $user = Auth::user();

        try {
            $file = $request->file('file');
            $content = file_get_contents($file->getRealPath());
            $data = json_decode($content, true);

            if (!$data || !isset($data['name']) || !isset($data['settings'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid template file format.'
                ], 400);
            }

            // Validate settings
            if (!\App\Models\FlashcardTemplate::validateSettings($data['settings'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid template settings.'
                ], 400);
            }

            // Check if template with same name exists
            $existing = \App\Models\FlashcardTemplate::where('user_id', $user->id)
                ->where('name', $data['name'])
                ->first();

            if ($existing) {
                // Add suffix to make it unique
                $data['name'] = $data['name'] . ' (imported)';
            }

            $template = \App\Models\FlashcardTemplate::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'settings' => $data['settings'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template imported successfully.',
                'template' => $template
            ]);

        } catch (\Exception $e) {
            Log::error('Template import failed:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to import template.'
            ], 500);
        }
    }
}