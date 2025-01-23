<?php

namespace App\Livewire;

use App\Models\Provider;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ProviderProducts extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;
    public function mount()
    {
        if (Gate::denies('viewAny', Provider::class)) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
    }
    public function render()
    {
        return view('livewire.provider-products');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Provider::query()->where('id', '!=', 1))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('razon_social')
                    ->label('Rázon Social')
                    ->searchable(),
                Tables\Columns\TextColumn::make('direccion')
                    ->label('Dirección')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
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
                    ->modalHeading('Editar Proveedor')
                    ->form([
                        Forms\Components\Grid::make(2) // Crear una cuadrícula con 2 columnas
                            ->schema([
                                Forms\Components\TextInput::make('razon_social')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('direccion')
                                    ->label('Dirección')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('telefono')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),
                            ]),
                    ])->authorize(fn (Provider $record) => Gate::allows('update', $record)),

                Tables\Actions\DeleteAction::make()
                    ->authorize(fn (Provider $record) => Gate::allows('delete', $record))
                    ->modalHeading('Borrar Proveedor')

            ])
            ->headerActions([

                Tables\Actions\CreateAction::make()
                    ->modalHeading('Crear Nuevo Proveedor')
                    ->label('Crear Nuevo')
                    ->model(Provider::class)

                    ->form([
                        Forms\Components\Grid::make(2) // Crear una cuadrícula con 2 columnas
                            ->schema([
                                Forms\Components\TextInput::make('razon_social')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('direccion')
                                    ->label('Dirección')
                                    ->default('sin descripcion')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('telefono')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->default('sin descripcion')
                                    ->email()
                                    ->maxLength(255),
                            ]),
                    ])
                    ->authorize(fn () => Gate::allows('create', Provider::class)),
            ])

            ->bulkActions([
                ExportBulkAction::make()

                ->authorize(fn () => Gate::allows('export', Provider::class)),
            ]);
    }
}
