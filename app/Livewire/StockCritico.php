<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class StockCritico extends Component Implements HasForms, HasTable
{

    use InteractsWithForms, InteractsWithTable;
    public function render()
    {

        
        return view('livewire.stock-critico', [
            'products' => Product::all(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
        ->query(Product::query()
        ->where('stock_actual', '<', 20)
    )
            ->defaultPaginationPageOption(option:5)
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('codigo_unico')
                ->label('Codigo')
                ->sortable(),
                Tables\Columns\TextColumn::make('category.descripcion')->label('Categoría')->searchable(),
                Tables\Columns\TextColumn::make('provider.razon_social')->label('Proveedor')->searchable(),
                Tables\Columns\TextColumn::make('deposit.nombre')->label('Depósito')->searchable(),
            Tables\Columns\TextColumn::make('nombre')
                ->searchable(),
            Tables\Columns\TextColumn::make('descripcion')
                ->searchable(),
            Tables\Columns\TextColumn::make('precio_actual')
               
                ->sortable(),
            Tables\Columns\TextColumn::make('stock_actual')
                
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}
