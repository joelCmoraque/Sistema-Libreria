<?php

namespace App\Livewire;

use App\Models\Output;
use App\Models\Input;
use App\Models\Product;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms;
use Closure;
use Exception;


class Salidas extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public $cantidad;
    public $product_id;

   

    public function render()
    {
        return view('livewire.salidas', [
            'products' => Product::all(),
        ]);
    }

  

    public function table(Table $table): Table
    {
         return $table
         ->query(Output::query())
         ->columns([
            Tables\Columns\TextColumn::make('id')
            ->searchable(),
            Tables\Columns\TextColumn::make('product.nombre')->label('productoo')->searchable()->numeric() ->sortable(),
       
                Tables\Columns\TextColumn::make('cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('documento_referencia')
                ->label('Archivo')
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
            ->form([  Forms\Components\Select::make('product_id')
            ->relationship('product', 'nombre')
            ->required()
            ->searchable()
            ->preload()
            ->reactive(),
            Forms\Components\TextInput::make('cantidad')
            ->required()
            ->numeric()
            ->live()
            ->rule(static function (Forms\Get $get): Closure {
                return static function ( $value, Closure $fail) use ($get) {
                    $productId = $get('product_id');
                    if ($productId) {
                        $product = Product::find($productId);
                        if ($product && $value > $product->stock_actual) {
                            $fail("La cantidad ingresada es superior al stock actual ({$product->stock_actual}).");
                        }
                    }
                };
            })
            ->afterStateUpdated(function (Forms\Components\TextInput $component) {
                $this->validateOnly($component->getStatePath());}),
            Forms\Components\DateTimePicker::make('fecha')
                ->required(),
           
            Forms\Components\TextInput::make('documento_referencia')
                ->maxLength(255),
        ]),
            Tables\Actions\DeleteAction::make()
         ])
         ->headerActions([
            
            Tables\Actions\CreateAction::make()
            ->modalHeading('Crear Nuevo')
             ->label('Crear Nuevo')
            ->model(Output::class)
            
            ->form([
                Forms\Components\Select::make('product_id')
                ->relationship('product', 'nombre')
                ->label('Producto')
                ->required()
                ->searchable()
                ->preload()
                ->reactive(),
                Forms\Components\TextInput::make('cantidad')
                ->required()
                ->numeric()
                ->live()
                ->rule(static function (Forms\Get $get): Closure {
                    return static function (string $attribute, $value, Closure $fail) use ($get) {
                        $productId = $get('product_id');
                        if ($productId) {
                            $product = Product::find($productId);
                            if ($product && $value > $product->stock_actual) {
                                $fail("La cantidad ingresada es superior al stock actual ({$product->stock_actual}).");
                            }
                        }
                    };
                })
                ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                    $this->validateOnly($component->getStatePath());}),
                 
                Forms\Components\TextInput::make('archivo')
                    ->maxLength(255),
            ])
            ]);



    }
   
}
