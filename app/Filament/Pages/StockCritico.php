<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class StockCritico extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.stock-critico';

    protected static bool $shouldRegisterNavigation = false;
}
