<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LearningRequest;
use App\Services\LearningService;
use App\Services\WordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller for learning operations.
 */
class LearningController extends Controller
{
    public function __construct(
        private LearningService $learningService,
        private WordService $wordService
    ) {}

    /**
     * Display the learning page with filtered words.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $filters = $request->session()->get('word_filters', []);
        
        // Get initial word for learning
        $word = $this->learningService->getNextRandomWord($user, $filters);
        
        // Get learning statistics
        $stats = $this->learningService->getLearningStats($user);
        
        return Inertia::render('Learning/Index', [
            'word' => $word,
            'filters' => $filters,
            'stats' => $stats,
            'user' => $user,
        ]);
    }

    /**
     * Get next random word for learning session (AJAX).
     */
    public function next(Request $request): JsonResponse
    {
        $user = $request->user();
        $filters = $request->session()->get('word_filters', []);
        $excludeIds = $request->input('exclude_ids', []);
        
        $word = $this->learningService->getNextRandomWord($user, $filters, $excludeIds);
        
        return response()->json([
            'word' => $word,
            'has_more' => $word !== null,
        ]);
    }

    /**
     * Mark word as learned.
     */
    public function markLearned(LearningRequest $request): JsonResponse
    {
        $user = $request->user();
        $wordId = $request->validated('word_id');
        
        $userWord = $this->learningService->markWordAsLearned($user, $wordId);
        
        return response()->json([
            'success' => true,
            'message' => 'Word marked as learned!',
            'user_word' => $userWord,
        ]);
    }

    /**
     * Add word to review list.
     */
    public function addToReview(LearningRequest $request): JsonResponse
    {
        $user = $request->user();
        $wordId = $request->validated('word_id');
        
        $userWord = $this->learningService->addWordToReview($user, $wordId);
        
        return response()->json([
            'success' => true,
            'message' => 'Word added to review list!',
            'user_word' => $userWord,
        ]);
    }

    /**
     * Start learning session with filtered words.
     */
    public function startSession(Request $request): \Illuminate\Http\RedirectResponse
    {
        $filters = $request->only(['topic', 'cefr_level', 'meaning_search', 'word_search']);
        
        // Store filters in session
        $request->session()->put('word_filters', $filters);
        
        return redirect()->route('learning.index');
    }

    /**
     * Get learning session words.
     */
    public function getSessionWords(Request $request): JsonResponse
    {
        $user = $request->user();
        $filters = $request->session()->get('word_filters', []);
        $count = $request->input('count', 10);
        
        $words = $this->learningService->getWordsForLearningSession($user, $filters, $count);
        
        return response()->json([
            'words' => $words,
            'total_count' => $words->count(),
        ]);
    }

    /**
     * Update learning progress.
     */
    public function updateProgress(Request $request): JsonResponse
    {
        $request->validate([
            'word_id' => 'required|integer|exists:words,id',
            'is_correct' => 'required|boolean',
        ]);

        $user = $request->user();
        $wordId = $request->input('word_id');
        $isCorrect = $request->input('is_correct');
        
        $userWord = $this->learningService->updateProgress($user, $wordId, $isCorrect);
        
        return response()->json([
            'success' => true,
            'user_word' => $userWord,
            'message' => $isCorrect ? 'Correct!' : 'Keep practicing!',
        ]);
    }
}