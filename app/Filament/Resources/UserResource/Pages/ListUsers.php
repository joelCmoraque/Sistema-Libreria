<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;
    protected static ?string $title = 'Usuarios';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->modalHeading('Borrar usuario')
            ->label('Crear Usuario'),
        ];
    }



  


}
