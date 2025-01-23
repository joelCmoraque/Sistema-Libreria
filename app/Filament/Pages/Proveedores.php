<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class Proveedores extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static string $view = 'filament.pages.proveedores';
    protected static ?string $navigationGroup = 'Inventario y Proveedores';

    public static function shouldRegisterNavigation(): bool
    {
        return Gate::allows('viewAny', \App\Models\Provider::class);
    }
}
