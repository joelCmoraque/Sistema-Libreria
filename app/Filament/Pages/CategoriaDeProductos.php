<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class CategoriaDeProductos extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string $view = 'filament.pages.categoria-de-productos';
    protected static bool $isLazy = false;

    protected static ?string $navigationGroup = 'Inventario y Proveedores';
    public static function shouldRegisterNavigation(): bool
    {
        return Gate::allows('viewAny', \App\Models\Category::class);
    }
}
