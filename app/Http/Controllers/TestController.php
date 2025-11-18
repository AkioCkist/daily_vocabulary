<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyTestRequest;
use App\Http\Requests\TestAnswerRequest;
use App\Services\TestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller for daily test operations.
 */
class TestController extends Controller
{
    public function __construct(
        private TestService $testService
    ) {}

    /**
     * Display the test page with filter board.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $testStats = $this->testService->getTestStats($user);
        
        return Inertia::render('Test/Index', [
            'test' => null, // Don't auto-generate, show filter board first
            'stats' => $testStats,
            'user' => $user,
        ]);
    }

    /**
     * Generate a quick daily test (10 random questions).
     */
    public function generateDaily(Request $request): Response
    {
        $user = $request->user();
        
        try {
            $test = $this->testService->generateDailyTest($user);
            $testStats = $this->testService->getTestStats($user);
            
            return Inertia::render('Test/Index', [
                'test' => $test->load(['items.word']),
                'stats' => $testStats,
                'message' => 'Daily test generated successfully!',
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Test/Index', [
                'test' => null,
                'stats' => $this->testService->getTestStats($user),
                'error' => 'Failed to generate daily test: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate a new custom test with user configuration.
     */
    public function generate(DailyTestRequest $request): Response
    {
        $user = $request->user();
        $config = $request->getTestConfig();
        
        try {
            $test = $this->testService->generateDailyTest($user, $config);
            $testStats = $this->testService->getTestStats($user);
            
            return Inertia::render('Test/Index', [
                'test' => $test->load(['items.word']),
                'stats' => $testStats,
                'message' => 'Custom test generated successfully!',
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Test/Index', [
                'test' => null,
                'stats' => $this->testService->getTestStats($user),
                'error' => 'Failed to generate custom test: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Submit an answer for a test question.
     */
    public function submitAnswer(TestAnswerRequest $request): JsonResponse
    {
        $user = $request->user();
        $answerData = $request->getAnswerData();
        
        try {
            $attempt = $this->testService->submitAnswer(
                $user,
                $answerData['daily_test_item_id'],
                $answerData['answer'],
                $answerData['time_taken']
            );
            
            return response()->json([
                'success' => true,
                'attempt' => $attempt->load(['word', 'dailyTestItem']),
                'is_correct' => $attempt->is_correct,
                'message' => $attempt->is_correct ? 'Correct!' : 'Incorrect, keep practicing!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting answer: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Complete the daily test and get final results.
     */
    public function complete(Request $request): Response
    {
        $request->validate([
            'test_id' => 'required|integer|exists:daily_tests,id',
            'answers' => 'required|array',
        ]);

        $user = $request->user();
        $testId = $request->input('test_id');
        $answers = $request->input('answers');
        
        $test = $user->dailyTests()->with(['items.word'])->findOrFail($testId);
        
        // Process answers and calculate results
        $results = [];
        $correctCount = 0;
        
        foreach ($test->items as $index => $item) {
            $userAnswer = $answers[$index] ?? '';
            $correctAnswer = $item->correct_answer;
            $isCorrect = false;
            
            if ($item->question_type === 'word_to_definition') {
                $isCorrect = trim($userAnswer) === trim($correctAnswer);
            } else {
                // For definition_to_word, be more flexible with matching
                $isCorrect = strtolower(trim($userAnswer)) === strtolower(trim($correctAnswer));
            }
            
            if ($isCorrect) {
                $correctCount++;
            }
            
            $results[] = [
                'question_index' => $index,
                'word_id' => $item->word->id,
                'word' => $item->word->word,
                'definition' => $item->word->definition,
                'question_type' => $item->question_type,
                'user_answer' => $userAnswer,
                'correct_answer' => $correctAnswer,
                'is_correct' => $isCorrect,
                'options' => $item->options,
                'adding_to_vocab' => false,
                'added_to_vocab' => false,
            ];
        }
        
        $score = $test->items->count() > 0 ? round(($correctCount / $test->items->count()) * 100) : 0;
        
        // Update test as completed
        $test->update([
            'is_completed' => true,
            'score' => $score,
        ]);
        
        $testStats = $this->testService->getTestStats($user);
        
        return Inertia::render('Test/Index', [
            'test' => $test,
            'stats' => $testStats,
            'testResults' => [
                'score' => $score,
                'correct_answers' => $correctCount,
                'total_questions' => $test->items->count(),
                'results' => $results,
            ],
            'message' => 'Test completed! Your score: ' . $score . '%',
        ]);
    }

    /**
     * Get test results and statistics.
     */
    public function results(Request $request): Response
    {
        $user = $request->user();
        $test = $this->testService->getTodaysTest($user);
        
        if (!$test || !$test->is_completed) {
            return Inertia::render('Test/Index', [
                'message' => 'No completed test found for today.',
                'user' => $user,
            ]);
        }
        
        $testStats = $this->testService->getTestStats($user);
        
        return Inertia::render('Test/Results', [
            'test' => $test->load(['items.word', 'attempts']),
            'stats' => $testStats,
            'user' => $user,
        ]);
    }

    /**
     * Get test history for user.
     */
    public function history(Request $request): Response
    {
        $user = $request->user();
        
        $tests = $user->dailyTests()
            ->where('is_completed', true)
            ->with(['items.word'])
            ->orderBy('date', 'desc')
            ->paginate(10);
        
        $testStats = $this->testService->getTestStats($user);
        
        return Inertia::render('Test/History', [
            'tests' => $tests,
            'stats' => $testStats,
            'user' => $user,
        ]);
    }

    /**
     * Get specific test details.
     */
    public function show(Request $request, string $testId): Response
    {
        $user = $request->user();
        
        $test = $user->dailyTests()
            ->with(['items.word', 'attempts'])
            ->findOrFail((int) $testId);
        
        return Inertia::render('Test/Show', [
            'test' => $test,
            'user' => $user,
        ]);
    }
}