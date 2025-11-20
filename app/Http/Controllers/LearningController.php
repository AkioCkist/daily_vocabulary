<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LearningRequest;
use App\Http\Requests\LearningSessionRequest;
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
     * Display the learning page with filter board.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $stats = $this->learningService->getLearningStats($user);
        
        return Inertia::render('Learning/Index', [
            'session' => null, // Don't auto-generate, show filter board first
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
    public function markLearned(LearningRequest $request)
    {
        $user = $request->user();
        $wordId = $request->validated('word_id');
        
        $userWord = $this->learningService->markWordAsLearned($user, $wordId);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Word marked as learned!',
                'user_word' => $userWord,
            ]);
        }
        
        return redirect()->back()->with('success', 'Word marked as learned!');
    }

    /**
     * Add word to review list.
     */
    public function addToReview(LearningRequest $request)
    {
        $user = $request->user();
        $wordId = $request->validated('word_id');
        
        $userWord = $this->learningService->addWordToReview($user, $wordId);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Word added to review list!',
                'user_word' => $userWord,
            ]);
        }
        
        return redirect()->back()->with('success', 'Word added to review list!');
    }

    /**
     * Generate a quick learning session (10 random words).
     */
    public function generateQuick(Request $request): Response
    {
        $user = $request->user();
        
        try {
            $sessionWords = $this->learningService->getWordsForLearningSession($user, [], 10);
            $stats = $this->learningService->getLearningStats($user);
            
            if ($sessionWords->isEmpty()) {
                // Fallback to any available words
                $sessionWords = $this->wordService->getNewWordsForUser($user->id, [], 10);
            }
            
            return Inertia::render('Learning/Index', [
                'session' => [
                    'words' => $sessionWords,
                    'config' => ['word_count' => 10, 'session_type' => 'mixed']
                ],
                'stats' => $stats,
                'message' => 'Quick learning session generated!',
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Learning/Index', [
                'session' => null,
                'stats' => $this->learningService->getLearningStats($user),
                'error' => 'Failed to generate learning session: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate a custom learning session with user configuration.
     */
    public function generateSession(LearningSessionRequest $request): Response
    {
        $user = $request->user();
        $config = $request->getSessionConfig();
        
        try {
            $sessionWords = $this->learningService->generateCustomSession($user, $config);
            $stats = $this->learningService->getLearningStats($user);
            
            return Inertia::render('Learning/Index', [
                'session' => [
                    'words' => $sessionWords,
                    'config' => $config
                ],
                'stats' => $stats,
                'message' => 'Custom learning session generated!',
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Learning/Index', [
                'session' => null,
                'stats' => $this->learningService->getLearningStats($user),
                'error' => 'Failed to generate custom session: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Start learning session with filtered words (legacy method for backward compatibility).
     */
    public function startSession(Request $request)
    {
        $filters = $request->only(['topic', 'cefr_level', 'word_count']);
        $user = $request->user();
        
        try {
            $wordCount = (int) ($filters['word_count'] ?? 10);
            $sessionWords = $this->learningService->getWordsForLearningSession($user, $filters, $wordCount);
            $stats = $this->learningService->getLearningStats($user);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'sessionWords' => $sessionWords,
                    'stats' => $stats,
                    'message' => 'Learning session started!'
                ]);
            }
            
            return Inertia::render('Learning/Index', [
                'session' => [
                    'words' => $sessionWords,
                    'config' => $filters
                ],
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to start session: ' . $e->getMessage(),
                ], 400);
            }
            
            return Inertia::render('Learning/Index', [
                'session' => null,
                'stats' => $this->learningService->getLearningStats($user),
                'error' => 'Failed to start session: ' . $e->getMessage(),
            ]);
        }
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
    public function updateProgress(Request $request)
    {
        $request->validate([
            'word_id' => 'required|integer|exists:words,id',
            'is_correct' => 'required|boolean',
        ]);

        $user = $request->user();
        $wordId = $request->input('word_id');
        $isCorrect = $request->input('is_correct');
        
        $userWord = $this->learningService->updateProgress($user, $wordId, $isCorrect);
        
        $message = $isCorrect ? 'Correct!' : 'Keep practicing!';
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'user_word' => $userWord,
                'message' => $message,
            ]);
        }
        
        return redirect()->back()->with('success', $message);
    }
}