<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\WordFilterRequest;
use App\Services\WordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller for word filtering and search operations.
 */
class WordFilterController extends Controller
{
    public function __construct(
        private WordService $wordService
    ) {}

    /**
     * Display filtered words based on user input.
     */
    public function index(WordFilterRequest $request): Response
    {
        $filters = $request->getFilters();
        $paginationParams = $request->getPaginationParams();

        $words = $this->wordService->filterWords($filters, $paginationParams['per_page']);
        $topics = $this->wordService->getTopics();
        $cefrLevels = $this->wordService->getCefrLevels();

        return Inertia::render('Words/Filter', [
            'words' => $words,
            'topics' => $topics,
            'cefrLevels' => $cefrLevels,
            'filters' => $filters,
            'user' => $request->user(),
        ]);
    }

    /**
     * Get filtered words via API (for AJAX requests).
     */
    public function api(WordFilterRequest $request): JsonResponse
    {
        $filters = $request->getFilters();
        $paginationParams = $request->getPaginationParams();

        $words = $this->wordService->filterWords($filters, $paginationParams['per_page']);

        return response()->json([
            'data' => $words->items(),
            'pagination' => [
                'current_page' => $words->currentPage(),
                'last_page' => $words->lastPage(),
                'per_page' => $words->perPage(),
                'total' => $words->total(),
            ]
        ]);
    }

    /**
     * Get filter options (topics and CEFR levels).
     */
    public function filterOptions(): JsonResponse
    {
        return response()->json([
            'topics' => $this->wordService->getTopics(),
            'cefr_levels' => $this->wordService->getCefrLevels(),
        ]);
    }

    /**
     * Advanced search with multiple criteria.
     */
    public function search(WordFilterRequest $request): Response
    {
        $filters = $request->getFilters();
        $paginationParams = $request->getPaginationParams();

        // Check if it's a general text search
        if ($request->filled('word_search') && !$request->filled(['topic', 'cefr_level', 'meaning_search'])) {
            $words = $this->wordService->searchWords(
                $request->input('word_search'),
                $paginationParams['per_page']
            );
        } else {
            $words = $this->wordService->filterWords($filters, $paginationParams['per_page']);
        }

        return Inertia::render('Words/Search', [
            'words' => $words,
            'topics' => $this->wordService->getTopics(),
            'cefrLevels' => $this->wordService->getCefrLevels(),
            'searchParams' => $filters,
            'user' => $request->user(),
        ]);
    }
}
