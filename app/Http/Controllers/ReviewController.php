<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller for review and practice operations.
 */
class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService
    ) {}

    /**
     * Display the review page with words that need practice.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        
        // Get user's vocabulary words for review (all words that are not mastered)
        $reviewWords = $user->userWords()
            ->with('word')
            ->where('mastered', '!=', true) // Include all words that are not mastered
            ->orderBy('last_seen_at', 'asc')
            ->take(20)
            ->get()
            ->map(function ($userWord) {
                return [
                    'id' => $userWord->word->id,
                    'word' => $userWord->word->word,
                    'definition' => $userWord->word->definition,
                    'pronunciation' => $userWord->word->pronunciation,
                    'example' => $userWord->word->example,
                    'level' => $userWord->word->cefr_level, // Fixed: was 'level', should be 'cefr_level'
                    'topic' => $userWord->word->topic,
                ];
            });

        // Simple stats - updated to count all vocabulary words, not just learned ones
        $stats = [
            'total_words' => $user->userWords()->count(),
            'learned_words' => $user->userWords()->where('is_learned', true)->count(),
            'review_words' => $user->userWords()->where('mastered', '!=', true)->count(),
            'mastered_words' => $user->userWords()->where('mastered', true)->count(),
        ];
        
        return Inertia::render('Review/Index', [
            'reviewWords' => $reviewWords,
            'stats' => $stats,
        ]);
    }

    /**
     * Start a review practice session.
     */
    public function practice(Request $request): Response
    {
        $user = $request->user();
        
        // Get a random word from user's vocabulary for practice
        $userWord = $user->userWords()
            ->with('word')
            ->where('mastered', '!=', true) // Include all words that are not mastered
            ->inRandomOrder()
            ->first();
        
        if (!$userWord) {
            return Inertia::render('Review/Index', [
                'reviewWords' => collect([]),
                'stats' => [
                    'total_words' => 0,
                    'learned_words' => 0,
                    'review_words' => 0,
                    'mastered_words' => 0,
                ],
                'message' => 'No words found in your vocabulary. Add some words first!',
            ]);
        }
        
        return Inertia::render('Review/Index', [
            'userWord' => $userWord,
            'word' => $userWord->word,
            'user' => $user,
        ]);
    }

    /**
     * Submit answer for review practice.
     */
    public function submitAnswer(Request $request)
    {
        $request->validate([
            'word_id' => 'required|integer|exists:words,id',
            'difficulty' => 'required|integer|min:1|max:5',
        ]);

        $user = $request->user();
        $wordId = $request->input('word_id');
        $difficulty = $request->input('difficulty');
        
        // Find user's word record
        $userWord = $user->userWords()->where('word_id', $wordId)->first();
        
        if ($userWord) {
            // Update based on difficulty rating
            $updates = ['last_seen_at' => now()];
            
            if ($difficulty <= 2) {
                // Hard - needs more practice
                $updates['consecutive_correct'] = 0;
                $updates['mistake_count'] = $userWord->mistake_count + 1;
            } elseif ($difficulty >= 4) {
                // Easy - progressing well
                $updates['consecutive_correct'] = $userWord->consecutive_correct + 1;
                
                // Mark as mastered if consistently easy
                if ($updates['consecutive_correct'] >= 3) {
                    $updates['mastered'] = true;
                }
            }
            
            $userWord->update($updates);
        }
        
        // Return JSON for AJAX requests, redirect for Inertia requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Rating recorded successfully!',
            ]);
        }
        
        // For Inertia requests, redirect back with a success message
        return redirect()->back()->with('success', 'Rating recorded successfully!');
    }

    /**
     * Get next random word for practice.
     */
    public function nextWord(Request $request)
    {
        $user = $request->user();
        
        // Get a random word from user's vocabulary
        $userWord = $user->userWords()
            ->with('word')
            ->where('mastered', '!=', true) // Include all words that are not mastered
            ->inRandomOrder()
            ->first();
        
        if (!$userWord) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'completed' => true,
                    'message' => 'All review words completed!',
                ]);
            }
            
            return redirect()->route('review.index')->with('success', 'All review words completed!');
        }
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'completed' => false,
                'userWord' => $userWord,
                'word' => $userWord->word,
            ]);
        }
        
        return Inertia::render('Review/Index', [
            'userWord' => $userWord,
            'word' => $userWord->word,
            'user' => $user,
        ]);
    }

    /**
     * Start intensive review for struggling words.
     */
    public function intensive(Request $request): Response
    {
        $user = $request->user();
        $minMistakes = $request->input('min_mistakes', 3);
        
        $strugglingWords = $this->reviewService->getIntensiveReviewWords($user, $minMistakes);
        
        return Inertia::render('Review/Index', [
            'strugglingWords' => $strugglingWords,
            'minMistakes' => $minMistakes,
            'user' => $user,
        ]);
    }

    /**
     * Get spaced repetition words for review.
     */
    public function spacedRepetition(Request $request): Response
    {
        $user = $request->user();
        $spacedWords = $this->reviewService->getSpacedRepetitionWords($user);
        
        return Inertia::render('Review/Index', [
            'spacedWords' => $spacedWords,
            'user' => $user,
        ]);
    }

    /**
     * Mark word as mastered (admin action).
     */
    public function markMastered(Request $request)
    {
        $request->validate([
            'word_id' => 'required|integer|exists:words,id',
        ]);

        $user = $request->user();
        $wordId = $request->input('word_id');
        
        $userWord = $this->reviewService->markWordAsMastered($user, $wordId);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'user_word' => $userWord,
                'message' => 'Word marked as mastered!',
            ]);
        }
        
        return redirect()->back()->with('success', 'Word marked as mastered!');
    }

    /**
     * Reset word to review state.
     */
    public function resetToReview(Request $request)
    {
        $request->validate([
            'word_id' => 'required|integer|exists:words,id',
        ]);

        $user = $request->user();
        $wordId = $request->input('word_id');
        
        $userWord = $this->reviewService->resetWordToReview($user, $wordId);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'user_word' => $userWord,
                'message' => 'Word reset to review status!',
            ]);
        }
        
        return redirect()->back()->with('success', 'Word reset to review status!');
    }
}