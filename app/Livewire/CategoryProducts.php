<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms;

class CategoryProducts extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;
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
            ->form([ Forms\Components\TextInput::make('nombre')
            ->required()
            ->maxLength(255),
        Forms\Components\TextInput::make('descripcion'),
        ]),
            Tables\Actions\DeleteAction::make()
            ->modalHeading('Borrar Categoría de Producto')
         ])
         ->headerActions([
            
            Tables\Actions\CreateAction::make()
            ->modalHeading('Crear Nueva Categoría')
             ->label('Crear Nuevo')
            ->model(Category::class)
            
            ->form([
                Forms\Components\TextInput::make('nombre')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('descripcion')
            ->label('Descripción'),
            ])
            ]);



    }
}
