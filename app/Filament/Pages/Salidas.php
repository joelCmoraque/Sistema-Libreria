<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class Salidas extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-left-start-on-rectangle';

    protected static string $view = 'filament.pages.salidas';
    protected static ?string $navigationGroup = 'Movimientos de Inventario';

    public static function shouldRegisterNavigation(): bool
    {
        return Gate::allows('viewAny', \App\Models\Output::class);
    }
}
