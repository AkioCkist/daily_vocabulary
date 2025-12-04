<?php

namespace App\Filament\Resources\DailyTestResource\Pages;

use App\Filament\Resources\DailyTestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDailyTests extends ListRecords
{
    protected static string $resource = DailyTestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
