<?php

namespace App\Livewire;

use App\Models\Category;
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

class CategoryProducts extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public function mount()
    {
        if (Gate::denies('viewAny', Category::class)) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
    }
    public function render()
    {
        return view('livewire.category-products');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Category::query())
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
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
                                Forms\Components\TextInput::make('descripcion'),
                            ]),
                    ])
                    ->authorize(fn (Category $record) => Gate::allows('update', $record)),
                
            ])
            ->headerActions([

                Tables\Actions\CreateAction::make()
                    ->modalHeading('Crear Nueva Categoría')
                    ->label('Crear Nuevo')
                    ->model(Category::class)

                    ->form([
                        Forms\Components\Grid::make(2) // Crear una cuadrícula con 2 columnas
                            ->schema([
                                Forms\Components\TextInput::make('nombre')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('descripcion')
                                    ->label('Descripción')
                                    ->default('sin descripcion'),
                            ]),
                    ])
                    ->authorize(fn () => Gate::allows('create', Category::class)),
            ])

            ->bulkActions([

                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()
                    ->authorize(fn () => Gate::allows('export', Category::class)),
                    Tables\Actions\DeleteBulkAction::make()
                    ->authorize(fn (Category $record) => Gate::allows('delete', $record))
                    ->modalHeading('Borrar Categoría de Producto')  ]),
            ]);
    }
}
