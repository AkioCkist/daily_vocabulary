<?php

namespace App\Filament\Widgets;

use App\Models\DailyTest;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TestCompletionTrendChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return 'Test Completion Trend';
    }

    protected function getData(): array
    {
        $data = DailyTest::where('is_completed', true)
            ->selectRaw('DATE(created_at)::date as date, COUNT(*) as count')
            ->groupByRaw('DATE(created_at)::date')
            ->orderByRaw('DATE(created_at)::date asc')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Tests Completed',
                    'data' => $data->pluck('count')->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.5,
                    'fill' => true,
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
