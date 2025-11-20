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

        return Inertia::render('Home', [
            'user' => $user,
            'dashboard' => $dashboardData,
        ]);
    }
}

