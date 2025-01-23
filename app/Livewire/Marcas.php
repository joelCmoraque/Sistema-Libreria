<?php

namespace App\Livewire;


use Livewire\Component;
use App\Models\Brand;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms;
use Illuminate\Support\Facades\Gate;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class Marcas extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;
    public function render()
    {
        return view('livewire.marcas');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Brand::query()->where('id', '!=', 1))
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
                    ->form([
                        Forms\Components\Grid::make(2) // Crear una cuadrícula con 2 columnas
                            ->schema([
                                Forms\Components\TextInput::make('nombre')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('descripcion')
                            ]),

                    ])
                    ->authorize(fn (Brand $record) => Gate::allows('update', $record)),

                Tables\Actions\DeleteAction::make()
                ->authorize(fn (Brand $record) => Gate::allows('delete', $record))
                ->modalHeading('Borrar Marca'),
            ])
            ->headerActions([

                Tables\Actions\CreateAction::make()
                    ->modalHeading('Crear Nuevo')
                    ->label('Crear Nuevo')
                    ->model(Brand::class)

                    ->form([
                        Forms\Components\Grid::make(2) // Crear una cuadrícula con 2 columnas
                            ->schema([
                                Forms\Components\TextInput::make('nombre')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('descripcion')
                                ->default('sin descripcion')
                                ,
                            ]),
                    ])
                    ->authorize(fn () => Gate::allows('create', Brand::class)),
            ])

            ->bulkActions([
                ExportBulkAction::make()
                ->authorize(fn () => Gate::allows('export', Brand::class))
            ]);
    }
}
