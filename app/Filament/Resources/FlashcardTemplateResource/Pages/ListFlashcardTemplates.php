<?php

namespace App\Filament\Resources\FlashcardTemplateResource\Pages;

use App\Filament\Resources\FlashcardTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFlashcardTemplates extends ListRecords
{
    protected static string $resource = FlashcardTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
