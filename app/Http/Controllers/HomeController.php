<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Services\DailyWordService;

class HomeController extends Controller
{
    public function index(DailyWordService $dailyWordService)
    {
        $wordOfTheDay = $dailyWordService->getTodayWord();

        return Inertia::render('Home', [
            'wordOfTheDay' => $wordOfTheDay,
        ]);
    }
}

