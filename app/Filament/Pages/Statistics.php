<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\Width;

class Statistics extends Page
{
    public function getTitle(): string
    {
        return 'Statistics & Analytics';
    }

    public function getMaxContentWidth(): string|Width|null
    {
        return Width::Full;
    }
}
