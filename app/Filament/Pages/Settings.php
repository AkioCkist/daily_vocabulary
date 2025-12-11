<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\Width;

class Settings extends Page
{
    protected static ?int $navigationSort = 20;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public function getView(): string
    {
        return 'filament.pages.settings';
    }

    public function getTitle(): string
    {
        return 'Application Settings';
    }

    public function getMaxContentWidth(): string|Width|null
    {
        return Width::Large;
    }
}

