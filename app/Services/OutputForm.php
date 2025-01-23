<?php

namespace App\Services;

use Filament\Forms;
use App\Models\Product;
use Closure;

final class OutputForm{
    public static function schema($componentInstance): array
    {
        return [
            Forms\Components\Grid::make(1)
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('product_id')->relationship('product', 'nombre')->label('Producto')->required()->searchable()
                                ->preload()->reactive()
                                ->reactive()
                                ->afterStateUpdated(function (callable $set, $state) {
                                    $product = Product::find($state);
                                    if ($product) {
                                        $set('precio_unitario', $product->precio_actual);
                                    } else {
                                        $set('precio_unitario', 0);
                                    }
                                }),
                            Forms\Components\TextInput::make('precio_unitario')
                                ->label('Precio')
                                ->required()
                                ->numeric()
                                ->readOnly()
                                ->afterStateUpdated(function (callable $set, callable $get) {
                                    $precioUnitario = $get('precio_unitario') ?: 0;
                                    $cantidad = $get('cantidad') ?: 0;
                                    $total = $precioUnitario * $cantidad;
                                    $set('total', $total);
                                })
                        ]),
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('cantidad')->required()->numeric()->live()->columnSpan(1)
                                ->rule(function (Forms\Get $get) use ($componentInstance): Closure {
                                    return function (string $attribute, $value, Closure $fail) use ($get, $componentInstance) {
                                        try {
                                            $componentInstance->validateQuantity($get('product_id'), $value);
                                            $componentInstance->validateQuantityMax($value);
                                        } catch (\Exception $e) {
                                            $fail($e->getMessage());
                                        }
                                    };
                                })
                                ->afterStateUpdated(function (callable $set, callable $get, Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $textInputComponent) use ($componentInstance) {
                                    $componentInstance->validateOnly($textInputComponent->getStatePath());
                                    $precioUnitario = $get('precio_unitario') ?: 0;
                                    $cantidad = $get('cantidad') ?: 0;
                                    $total = $precioUnitario * $cantidad;
                                    $set('total', $total);
                                }),
                            Forms\Components\TextInput::make('total')->required()->readOnly()->numeric()->live(),
                        ]),
                ]),
        ];
    }
}