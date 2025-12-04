<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AverageScoresChart;
use App\Filament\Widgets\LatestTestsTable;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\TestCompletionTrendChart;
use App\Filament\Widgets\UserGrowthChart;
use App\Filament\Widgets\WordDifficultyDistributionChart;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;

class Statistics extends Page
{
    protected static ?int $navigationSort = 10;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-chart-bar';
    }

    public function getView(): string
    {
        return 'filament.pages.statistics';
    }

    public function getTitle(): string
    {
        return 'Statistics & Analytics';
    }

    public function getMaxContentWidth(): string|Width|null
    {
        return Width::Full;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            TestCompletionTrendChart::class,
            WordDifficultyDistributionChart::class,
            UserGrowthChart::class,
            AverageScoresChart::class,
            LatestTestsTable::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 2;
    }
}
