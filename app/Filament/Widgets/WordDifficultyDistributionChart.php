<?php

namespace App\Filament\Widgets;

use App\Models\Word;
use Filament\Widgets\ChartWidget;

class WordDifficultyDistributionChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return 'Word Difficulty Distribution';
    }

    protected function getData(): array
    {
        $data = Word::whereNotNull('cefr_level')
            ->selectRaw('cefr_level, COUNT(*) as count')
            ->groupBy('cefr_level')
            ->orderBy('cefr_level', 'asc')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Words by CEFR Level',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                        '#ec4899',
                    ],
                    'borderColor' => '#fff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $data->pluck('cefr_level')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
