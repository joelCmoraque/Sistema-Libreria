<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

use Closure;

class User extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public function render()
    {
        return view('livewire.user');
    }

    public function table(Table $table): Table
    {
         return $table
         ->query(User::query())
         ->columns([
            Tables\Columns\TextColumn::make('nombre')
            ->searchable(),
        Tables\Columns\TextColumn::make('descripcion')
            ->searchable(),
        Tables\Columns\TextColumn::make('created_at')
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('updated_at')
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true),
         ])
         ->actions([
            Tables\Actions\EditAction::make()
            ->form([ Forms\Components\TextInput::make('nombre')
            ->required()
            ->maxLength(255),
        Forms\Components\TextInput::make('descripcion'),
        ]),
            Tables\Actions\DeleteAction::make()
         ])
         ->headerActions([
            
            Tables\Actions\CreateAction::make()
            ->modalHeading('Crear Nuevo')
             ->label('Crear Nuevo')
            ->model(Deposit::class)
            
            ->form([
                Forms\Components\TextInput::make('nombre')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('descripcion'),
            ])
            ]);



    }
}
