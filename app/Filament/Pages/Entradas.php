<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class Entradas extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-left-end-on-rectangle';

    protected static string $view = 'filament.pages.entradas';

    protected static ?string $navigationGroup = 'Movimientos de Inventario';

    public static function shouldRegisterNavigation(): bool
    {
        return Gate::allows('viewAny', \App\Models\Input::class);
    }
}
