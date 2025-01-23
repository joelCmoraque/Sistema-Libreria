<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
    protected static ?string $title = 'Editar datos de usuario';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make('Borrar usuario')
            ->modalHeading('Borrar usuario'),
        ];
    }
}
