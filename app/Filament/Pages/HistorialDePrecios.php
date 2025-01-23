<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class HistorialDePrecios extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.historial-de-precios';
    protected static ?string $navigationGroup = 'Inventario y Proveedores';
    protected static bool $shouldRegisterNavigation = false;
}
