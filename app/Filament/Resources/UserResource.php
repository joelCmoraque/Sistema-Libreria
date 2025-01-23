<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\Rule;

class UserResource extends Resource

{

    public static function shouldRegisterNavigation(): bool
    {
        // Permitir que solo 'admin' vea la opción de usuarios en el menú de navegación
        return Gate::allows('viewAny', \App\Models\User::class) && auth()->user()->hasRole('admin');
    }
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Info Usuarios';
    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Información personal')
                    ->columns(3)
                    ->schema([
                        // ...
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->live()
                            ->rule(function () {
                                return function ($attribute, $value, $fail) {

                                    if (empty($value)) {
                                        return $fail(__('El campo :attribute es obligatorio.'));
                                    }
                                    if (User::where('name', $value)->exists()) {
                                        return $fail(__('El nombre ya ha sido tomado.'));
                                    }
                                };
                            }),
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

                        Forms\Components\Select::make('permisos')
                            ->relationship('permissions', 'name')
                            ->label('Permisos')
                            ->multiple()
                            ->preload()
                            ->searchable(),


                    ])



            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
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
            ->filters([

                SelectFilter::make('name')
                    ->options([
                        'joel' => 'JOEL',
                        'p24' => 'P23'
                    ])
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ->modalHeading('Borrar usuario')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
