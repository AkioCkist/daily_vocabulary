<?php

namespace App\Filament\Resources\FlashcardTemplateResource\Pages;

use App\Filament\Resources\FlashcardTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFlashcardTemplate extends EditRecord
{
    protected static string $resource = FlashcardTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
