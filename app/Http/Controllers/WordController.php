<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\WordRepositoryInterface;

class WordController extends Controller
{
    public function index(Request $request, WordRepositoryInterface $wordRepo)
    {
        $query = $request->input('q');
        $words = $query ? $wordRepo->search($query) : $wordRepo->all(50);

        return Inertia::render('Words/Index', [
            'words' => $words,
            'query' => $query,
        ]);
    }
}

