<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class Usuarios extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.usuarios';

    protected static bool $shouldRegisterNavigation = false;

 /*   public static function shouldRegisterNavigation(): bool
    {
        return Gate::allows('viewAny', \App\Models\User::class);
    }*/
}
