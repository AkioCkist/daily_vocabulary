<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\Width;

class Settings extends Page
{
    public function getTitle(): string
    {
        return 'Application Settings';
    }

    public function getMaxContentWidth(): string|Width|null
    {
        return Width::Large;
    }
}
