<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserVocabularyService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserWordController extends Controller
{
    public function index(UserVocabularyService $vocabService)
    {
        $userId = Auth::id();
        $userWords = $vocabService->getUserWords($userId);

        // Extract just the word data from the UserWord relationship
        $words = $userWords->map(function ($userWord) {
            return $userWord->word;
        })->values();

        return Inertia::render('UserVocabulary', [
            'userWords' => $words,
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
            ],
            'user' => auth()->user(),
        ]);
    }

    public function store(Request $request, UserVocabularyService $vocabService)
    {
        $validated = $request->validate([
            'word_id' => 'required|exists:words,id',
        ]);

        $vocabService->addWord(Auth::id(), $validated['word_id']);

        return redirect()->back()->with('success', 'Word added to your list!');
    }

    public function destroy($id, UserVocabularyService $vocabService)
    {
        $vocabService->removeWord(Auth::id(), $id);

        return redirect()->back()->with('success', 'Word removed from your list!');
    }
}

