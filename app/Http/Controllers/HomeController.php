<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {
    }

    public function index()
    {
        $user = Auth::user();
        $dashboardData = $user ? $this->dashboardService->getDashboardData($user) : null;

        // Get user's saved sessions for authenticated users
        $savedSessions = null;
        if ($user) {
            $savedSessions = \App\Models\SavedSession::forUser($user->id)
                ->recent()
                ->with([
                    'items' => function ($query) {
                        $query->orderBy('position');
                    }
                ])
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

    public function getStatsByDayRange($days)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validate days parameter
        if (!in_array($days, [1, 7, 30])) {
            return response()->json(['error' => 'Invalid day range'], 400);
        }

        $stats = $this->dashboardService->getUserStatsByDayRange($user, (int) $days);

        return response()->json($stats);
    }

    public function getMemoryReport($days)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validate days parameter
        if (!in_array($days, [1, 7, 30])) {
            return response()->json(['error' => 'Invalid day range'], 400);
        }

        $report = $this->dashboardService->getMemoryReportData($user, (int) $days);

        return response()->json($report);
    }
}

