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
                    $session->word_count = $session->items->count();
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

    public function exportMemoryReport($days)
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

        // Generate CSV content
        $csv = $this->generateMemoryReportCsv($report, $days);

        // Create filename with current date
        $filename = 'memory-report-' . $days . 'd-' . now()->format('Y-m-d') . '.csv';

        // Return CSV download response
        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function generateMemoryReportCsv($report, $days)
    {
        $output = fopen('php://temp', 'r+');

        // Add header section
        fputcsv($output, ['Memory Report - Last ' . $days . ' Day(s)']);
        fputcsv($output, ['Generated on: ' . now()->format('Y-m-d H:i:s')]);
        fputcsv($output, []);

        // Add summary statistics
        fputcsv($output, ['SUMMARY STATISTICS']);
        fputcsv($output, ['Total Attempts', $report['summary']['total_attempts']]);
        fputcsv($output, ['Accuracy', $report['summary']['accuracy'] . '%']);
        fputcsv($output, ['Words Practiced', $report['summary']['words_practiced']]);
        fputcsv($output, ['Study Sessions', $report['summary']['study_sessions']]);
        fputcsv($output, []);

        // Add frequently forgotten words
        fputcsv($output, ['FREQUENTLY FORGOTTEN WORDS']);
        if (count($report['frequently_forgotten']) > 0) {
            fputcsv($output, ['Word', 'Definition', 'Total Attempts', 'Correct', 'Incorrect', 'Accuracy']);
            foreach ($report['frequently_forgotten'] as $word) {
                fputcsv($output, [
                    $word['word'],
                    $word['definition'],
                    $word['total_attempts'],
                    $word['correct_count'],
                    $word['incorrect_count'],
                    $word['accuracy'] . '%'
                ]);
            }
        } else {
            fputcsv($output, ['No forgotten words in this period']);
        }
        fputcsv($output, []);

        // Add frequently remembered words
        fputcsv($output, ['FREQUENTLY REMEMBERED WORDS']);
        if (count($report['frequently_remembered']) > 0) {
            fputcsv($output, ['Word', 'Definition', 'Total Attempts', 'Correct', 'Incorrect', 'Accuracy']);
            foreach ($report['frequently_remembered'] as $word) {
                fputcsv($output, [
                    $word['word'],
                    $word['definition'],
                    $word['total_attempts'],
                    $word['correct_count'],
                    $word['incorrect_count'],
                    $word['accuracy'] . '%'
                ]);
            }
        } else {
            fputcsv($output, ['No remembered words in this period']);
        }
        fputcsv($output, []);

        // Add daily performance data
        fputcsv($output, ['DAILY PERFORMANCE']);
        if (count($report['daily_performance']) > 0) {
            fputcsv($output, ['Date', 'Attempts', 'Correct', 'Incorrect', 'Accuracy']);
            foreach ($report['daily_performance'] as $day) {
                fputcsv($output, [
                    $day['date'],
                    $day['attempts'],
                    $day['correct'],
                    $day['incorrect'],
                    $day['accuracy'] . '%'
                ]);
            }
        } else {
            fputcsv($output, ['No activity in this period']);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}

