<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class Welcome extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static string $view = 'filament.pages.welcome';
    protected static ?string $navigationLabel = 'Bienvenido';
    protected static ?string $title = 'INFORMACIÓN';
    protected static bool $isLazy = false;



    public static function shouldRegisterNavigation(): bool
    {
        return Gate::allows('viewAny', self::class);
    }
}
