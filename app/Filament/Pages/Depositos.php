<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class Depositos extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static string $view = 'filament.pages.depositos';
    protected static bool $isLazy = false;
    protected static ?string $navigationGroup = 'Inventario y Proveedores';
    public static function shouldRegisterNavigation(): bool
    {
        return Gate::allows('viewAny', \App\Models\Deposit::class);
    }
    public function getTabs(): array 
    {
        return [
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\MarkdownEditor::make('content'),
            // ...
        ];
    } 
}
