<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WordFilterRequest;
use App\Services\WordService;
use Illuminate\Http\JsonResponse;

class WordSearchController extends Controller
{
    public function __construct(
        private WordService $wordService
    ) {}

    /**
     * Search for words based on filters
     */
    public function search(WordFilterRequest $request): JsonResponse
    {
        $filters = $request->getFilters();
        $words = $this->wordService->filterWords($filters, 20); // Limit to 20 results

        return response()->json([
            'words' => $words->items(),
            'total' => $words->total(),
            'current_page' => $words->currentPage(),
            'last_page' => $words->lastPage(),
        ]);
    }
}