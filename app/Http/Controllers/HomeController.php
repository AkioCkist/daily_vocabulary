<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    public function index()
    {
        $user = Auth::user();
        $dashboardData = $user ? $this->dashboardService->getDashboardData($user) : null;
        
        // Get user's saved sessions for authenticated users
        $savedSessions = null;
        if ($user) {
            $savedSessions = \App\Models\SavedSession::forUser($user->id)
                ->recent()
                ->with(['items' => function($query) {
                    $query->orderBy('position');
                }])
                ->limit(6) // Show latest 6 saved sessions on dashboard
                ->get()
                ->map(function ($session) {
                    $session->flashcard_count = $session->items->count();
                    return $session;
                });
        }

        return Inertia::render('Home', [
            'user' => $user,
            'dashboard' => $dashboardData,
            'saved_sessions' => $savedSessions,
        ]);
    }
}

