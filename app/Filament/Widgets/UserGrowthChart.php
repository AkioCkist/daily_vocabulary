<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserGrowthChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return 'User Growth Over Time';
    }

    protected function getData(): array
    {
        $data = User::selectRaw('DATE(created_at)::date as date, COUNT(*) as count')
            ->groupByRaw('DATE(created_at)::date')
            ->orderByRaw('DATE(created_at)::date asc')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $data->pluck('count')->toArray(),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
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
