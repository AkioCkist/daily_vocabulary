<?php

namespace App\Filament\Widgets;

use App\Models\DailyTest;
use Filament\Widgets\ChartWidget;

class AverageScoresChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return 'Test Score Distribution';
    }

    protected function getData(): array
    {
        $scores = DailyTest::whereNotNull('score')
            ->selectRaw('ROUND(score / 10) * 10 as score_range, COUNT(*) as count')
            ->groupBy('score_range')
            ->orderBy('score_range', 'asc')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Tests',
                    'data' => $scores->pluck('count')->toArray(),
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#059669',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $scores->pluck('score_range')->map(fn ($score) => "{$score} pts")->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
