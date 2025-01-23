<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms;
use Closure;

class Usuarios extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public function mount()
    {
        if (Gate::denies('viewAny', User::class)) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
    }
    public function render()
    {
        return view('livewire.usuarios');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->form([
                        Forms\Components\TextInput::make('name')
                    ->required(),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required(),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->hiddenOn('edit')
                        ->required(),
            

                    Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                    ]),
                Tables\Actions\DeleteAction::make()
                ->authorize(fn (User $record) => Gate::allows('delete', $record)),
            ])
            ->headerActions([

                Tables\Actions\CreateAction::make()
                    ->modalHeading('Crear Nuevo')
                    ->label('Crear Nuevo')
                    ->model(User::class)

                    ->form([
                        Forms\Components\TextInput::make('name')
                        ->required(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->hiddenOn('edit')
                            ->required(),
                
    
                        Forms\Components\Select::make('roles')
                        ->relationship('roles', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable(),
                    ])
                    ->authorize(fn () => Gate::allows('create', User::class)),
            ]);
    }
}
