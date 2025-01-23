<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class Productos extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-Rectangle-Group';

    protected static string $view = 'filament.pages.productos';
    protected static ?string $navigationGroup = 'Inventario y Proveedores';

    public static function shouldRegisterNavigation(): bool
    {
        return Gate::allows('viewAny', \App\Models\Product::class);
    }
}
