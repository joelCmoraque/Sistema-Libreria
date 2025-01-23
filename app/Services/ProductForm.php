<?php

namespace App\Services;

use Filament\Forms;
use Closure;

final class ProductForm{

    public static function schema(): array{
        return[
            Forms\Components\Grid::make(1) // Cuadrícula principal con una sola columna para contener las cuadrículas internas
            ->schema([
                Forms\Components\Grid::make(4) // Cuadrícula interna con cuatro columnas para 'category_id', 'provider_id', 'deposit_id' y 'brand_id'
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Categoría')
                            ->relationship('category', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('provider_id')
                            ->relationship('provider', 'razon_social')
                            ->label('Proveedor')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('deposit_id')
                            ->relationship('deposit', 'nombre')
                            ->label('Depósito')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('brand_id')
                            ->relationship('brand', 'nombre')
                            ->label('Marca')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ]),
                    Forms\Components\Grid::make(1) // Cuadrícula principal con una sola columna para contener las cuadrículas internas
            ->schema([
                Forms\Components\Grid::make(5) // Cuadrícula interna con cuatro columnas para 'nombre', 'unidad_medida', 'precio_actual' y 'stock_actual'
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255)
                            ->live()
                            ->live() // Hacer que el campo sea reactivo
                            ->rule(function (Forms\Get $get): Closure {
                                return function (string $attribute, $value, Closure $fail) use ($get) {
                                    try {
                                        $this->validarNombre($get('nombre'), $value);
                                    } catch (\Exception $e) {
                                        $fail($e->getMessage());
                                    }
                                };
                            })
                            
                            ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                $this->validateOnly($component->getStatePath());
                            })
                            ->columnSpan(['sm' => 2, 'md' => 3, 'lg' => 2, 'full' => true]),
                        Forms\Components\Select::make('unidad_medida')
                        ->options([
                            'draft' => 'Draft',
                            'reviewing' => 'Reviewing',
                            'published' => 'Published',
                        ])
                        ->maxLength(255)
                        ->columnSpan(['sm' => 1, 'md' => 1, 'lg' => 1, 'full' => true]),
                        Forms\Components\TextInput::make('precio_actual')
                            ->required()
                            ->numeric()   ->columnSpan(['sm' => 1, 'md' => 1, 'lg' => 1, 'full' => true]),
                        Forms\Components\TextInput::make('stock_actual')
                            ->required()
                            ->numeric()    ->columnSpan(['sm' => 1, 'md' => 1, 'lg' => 1, 'full' => true]),
                    ])
                ]),
                Forms\Components\Grid::make(2) // Cuadrícula interna con dos columnas para los campos restantes
                    ->schema([
                        Forms\Components\TextInput::make('descripcion')
                            ->label('Descripción')
                            ->columnSpan('full'), // 'descripcion' ocupa toda la fila
                    ]),

                    
            ])
           
        ];
    }
}