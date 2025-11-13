<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserVocabularyService;
use Illuminate\Support\Facades\Auth;

class UserWordController extends Controller
{
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

