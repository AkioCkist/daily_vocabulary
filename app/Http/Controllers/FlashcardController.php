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
    ) {}

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
            'mode' => 'required|in:basic,advanced,topic,quick,review',
            'flashcard_type' => 'required|in:standard,fill_blank,mixed',
            'cefr_levels' => 'nullable|array',
            'cefr_levels.*' => 'string|in:A1,A2,B1,B2,C1,C2',
            'topic_ids' => 'nullable|array',
            'topic_ids.*' => 'integer|exists:topics,id',
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

        return Inertia::render('Flashcards/Practice', [
            'words' => $words,
            'settings' => $request->all(),
            'userTopics' => $userTopics,
        ]);
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
        $session = session('flashcard_session');
        
        if (!$session) {
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

        // Clear session
        session()->forget('flashcard_session');

        // If it's an Inertia request with preserveState, just return success
        if (request()->header('X-Inertia') && request()->header('X-Inertia-Partial-Data')) {
            return response()->noContent();
        }

        return redirect()->route('home')->with([
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

        // Get random words
        $words = $query->inRandomOrder()
            ->limit($settings['word_count'])
            ->get(['id', 'word', 'pronunciation', 'definition', 'example', 'cefr_level', 'topic']);

        return $words->toArray();
    }
}