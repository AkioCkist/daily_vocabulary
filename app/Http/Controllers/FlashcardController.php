<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        $request->validate([
            'mode' => 'required|in:basic,advanced,topic,quick',
            'word_count' => 'required|integer|min:5|max:50',
            'cefr_levels' => 'nullable|array',
            'cefr_levels.*' => 'string|in:A1,A2,B1,B2,C1,C2',
            'topic_ids' => 'nullable|array',
            'topic_ids.*' => 'integer|exists:topics,id',
        ]);

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

        return Inertia::render('Flashcards/Practice', [
            'words' => $words,
            'settings' => $request->all(),
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
            'is_correct' => 'required|boolean',
            'response_time' => 'nullable|integer|min:0',
        ]);

        $user = Auth::user();
        
        // Update user progress
        $userProgressService = app(\App\Services\UserProgressService::class);
        $userProgressService->updateWordProgress(
            $user,
            $request->get('word_id'),
            $request->get('is_correct')
        );

        // Record test attempt
        \App\Models\TestAttempt::create([
            'user_id' => $user->id,
            'word_id' => $request->get('word_id'),
            'is_correct' => $request->get('is_correct'),
            'answer_text' => $request->get('is_correct') ? 'Flashcard - Correct' : 'Flashcard - Incorrect',
            'time_taken' => $request->get('response_time'),
        ]);

        // Return JSON response for AJAX requests
        return response()->json(['success' => true], 200);
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
     * Generate flashcards based on settings.
     */
    private function generateFlashcards(array $settings): array
    {
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
            $topicNames = \App\Models\Topic::whereIn('id', $settings['topic_ids'])->pluck('name');
            $query->whereIn('topic', $topicNames);
        }

        // Get random words
        $words = $query->inRandomOrder()
            ->limit($settings['word_count'])
            ->get(['id', 'word', 'pronunciation', 'definition', 'example', 'cefr_level', 'topic']);

        return $words->toArray();
    }
}