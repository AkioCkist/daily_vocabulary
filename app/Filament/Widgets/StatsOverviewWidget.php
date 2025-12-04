<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Word;
use App\Models\Topic;
use App\Models\DailyTest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Active users in the system')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Words', Word::count())
                ->description('Words in the vocabulary database')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info'),

            Stat::make('Total Topics', Topic::count())
                ->description('Available topics')
                ->descriptionIcon('heroicon-m-tag')
                ->color('warning'),

            Stat::make('Completed Tests', DailyTest::where('is_completed', true)->count())
                ->description('Tests completed by users')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
