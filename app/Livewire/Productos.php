<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class Productos extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;
    public function render()
    {
        // Obtener todos los productos con stock por debajo del óptimo
        $lowStockProducts = Product::where('stock_actual', '<', 20)->get();

        // Obtener el usuario autenticado
        $user = Auth::user();

        if ($user && $lowStockProducts->isNotEmpty()) {
            $productList = $lowStockProducts->pluck('nombre')->implode(', ');
            $totalLowStockProducts = $lowStockProducts->count(); 

            Notification::make()
                ->title('Productos con stock bajo')
                ->body('Hay un total de '. $totalLowStockProducts .' productos con stock por debajo del óptimo')
                ->actions([
                    Action::make('Revisar')
                    ->button()
                    ->url(route('stock-critico'))
                ])
                ->sendToDatabase($user); // Enviar notificación a la base de datos del usuario autenticado
        }

        return view('livewire.productos');
    }

    public function table(Table $table): Table
    {
         return $table
         ->query(Product::query())
         ->columns([
            Tables\Columns\TextColumn::make('codigo_unico')
            ->label('Codigo')
            ->sortable(),
            Tables\Columns\TextColumn::make('category.nombre')->label('Categoría')->searchable(),
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
         ])
         ->actions([
            Tables\Actions\EditAction::make()
            ->form([  Forms\Components\Select::make('category_id')
            ->label('Categoria')
            ->relationship('category', 'descripcion')
            ->required()
            ->searchable()
            ->preload(),
        Forms\Components\Select::make('provider_id')
        ->relationship('provider', 'razon_social')
        ->required()
        ->searchable()
        ->preload(),
        Forms\Components\Select::make('deposit_id')
        ->relationship('deposit', 'nombre')
        ->required()
        ->searchable()
        ->preload(),
        Forms\Components\TextInput::make('nombre')
            ->required()
            ->maxLength(255),
        Forms\Components\TextInput::make('descripcion'),
        Forms\Components\TextInput::make('precio_actual')
            ->required()
            ->numeric(),
        Forms\Components\TextInput::make('stock_actual')
            ->required()
            ->numeric(),
        ]),
            Tables\Actions\DeleteAction::make()
         ])
         ->headerActions([
            
            Tables\Actions\CreateAction::make()
            ->modalHeading('Crear Nuevo')
             ->label('Crear Nuevo')
            ->model(Product::class)
            
            ->form([
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
            Forms\Components\TextInput::make('nombre')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('descripcion')
            ->label('Descripción'),
            
            Forms\Components\TextInput::make('precio_actual')
                ->required()
                ->numeric(),
            Forms\Components\TextInput::make('stock_actual')
                ->required()
                ->numeric(),
            ])
            ]);



    }
}
