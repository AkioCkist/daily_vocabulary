<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Services\DailyWordService;

class HomeController extends Controller
{
    public function index(DailyWordService $dailyWordService)
    {
        $userId = auth()->id();
        $wordOfTheDay = $dailyWordService->getTodayWord($userId);

        return Inertia::render('Home', [
            'wordOfTheDay' => $wordOfTheDay,
            'user' => auth()->user(),
        ]);
    }
}

