<?php

namespace App\Filament\Pages;

use Illuminate\Support\Facades\Gate;
use Filament\Pages\Page;


class Marcas extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static string $view = 'filament.pages.marcas';

    protected static ?string $navigationGroup = 'Inventario y Proveedores';

    public static function shouldRegisterNavigation(): bool
    {
        return Gate::allows('viewAny', \App\Models\Brand::class);
    }
}
