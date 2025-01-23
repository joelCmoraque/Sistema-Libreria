<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class tableInf extends BaseWidget
{
    protected static ?string $heading = 'Lista de Productos tiempo de reposicion';
    protected static bool $isLazy = false;
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan='full';

    public function table(Table $table): Table
    {
       
        return $table
        ->query(Product::query()
            ->select('products.*', DB::raw('ROUND(AVG(EXTRACT(DAY FROM (NOW() - inputs.fecha_entrada))), 2) AS tiempo_reposicion_promedio'))
            ->join('inputs', 'products.id', '=', 'inputs.product_id')
            
            ->groupBy('products.id')
        )
        ->defaultPaginationPageOption(option: 5)
        ->defaultSort('created_at', 'desc')
        ->columns([
            Tables\Columns\TextColumn::make('codigo_unico')
                ->label('Código')
                ->sortable(),
            Tables\Columns\TextColumn::make('deposit.nombre')->label('Depósito')->searchable(),
            Tables\Columns\TextColumn::make('nombre')
                ->searchable(),
            Tables\Columns\TextColumn::make('precio_actual')
                ->sortable(),
            Tables\Columns\TextColumn::make('stock_actual')
                ->sortable(),
            Tables\Columns\TextColumn::make('tiempo_reposicion_promedio')
                ->label('Tiempo de Reposición Promedio (días)')
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
