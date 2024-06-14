<?php

namespace App\Livewire;

use App\Models\Input;
use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms;
use Barryvdh\DomPDF\Facade\Pdf;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Blade;


class Entradas extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;
    
    public function render()
    {
        return view('livewire.entradas');
    }

    public function table(Table $table): Table
    {
         return $table
         ->query(Input::query())
         ->columns([
            Tables\Columns\TextColumn::make('product.nombre')
            ->label('Producto')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider.razon_social')
                ->label('Proveedor')
                    ->numeric()
                    ->sortable()
                      ->searchable(),
                Tables\Columns\TextColumn::make('cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_unitario')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('documento_referencia')
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
            ->preload(),
                Forms\Components\Select::make('provider_id')
                ->relationship('provider', 'razon_social')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('cantidad')
                ->required()
                ->numeric(),
            Forms\Components\DateTimePicker::make('fecha')
                ->required(),
            Forms\Components\TextInput::make('precio_unitario')
                ->required()
                ->numeric(),
            Forms\Components\TextInput::make('documento_referencia')
                ->maxLength(255),
        ]),
            Tables\Actions\DeleteAction::make(),
            Tables\Actions\Action::make('pdf') 
            ->label('PDF')
            ->color('success')
            ->icon('heroicon-o-document')
            ->action(function () {
                // Obtener todos los registros de la tabla, por ejemplo, de la tabla "records"
                $records = Input::all();
                
                if ($records->isNotEmpty()) {
                    return response()->streamDownload(function () use ($records) {
                        echo Pdf::loadView('livewire/report', ['records' => $records])->stream();
                    }, 'todos_los_registros.pdf');
                } else {
                    // Manejar el caso en que no se encontraron registros
                    return redirect()->back()->with('error', 'No se encontraron registros');
                }
            }),
       
         ])
         ->headerActions([

           
            Tables\Actions\Action::make('pdf') 
            ->label('PDF')
            ->color('success')
            ->icon('heroicon-o-document')
                      ->action(function () {
                // Obtener los filtros activos de Filament
                $filters = request()->input('filters');
        
                // Iniciar la consulta con todos los registros
                $query = Input::query();
        
                // Aplicar los filtros activos a la consulta
                if (!empty($filters)) {
                    foreach ($filters as $filter => $value) {
                        if ($value !== '') {
                            $query->where($filter, $value);
                        }
                    }
                }
        
                // Obtener los registros filtrados
                $records = $query->get();
        
                if ($records->isNotEmpty()) {
                    return response()->streamDownload(function () use ($records) {
                        echo Pdf::loadView('livewire.report', ['records' => $records])->stream();
                    }, 'registros_filtrados.pdf');
                } else {
                    // Manejar el caso en que no se encontraron registros
                    return redirect()->back()->with('error', 'No se encontraron registros');
                }
            }),
            Tables\Actions\CreateAction::make()
            ->modalHeading('Crear Nuevo')
             ->label('Crear Nuevo')
            ->model(Input::class)
            
            ->form([
                Forms\Components\Select::make('product_id')
                ->relationship('product', 'nombre')
                ->label('Producto')
                ->required()
                ->searchable()
                ->preload(),
                    Forms\Components\Select::make('provider_id')
                    ->relationship('provider', 'razon_social')
                    ->label('Proveedor')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('cantidad')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('precio_unitario')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('documento_referencia')
                    ->maxLength(255),
            ])
            ,
           ])
            ;



    }
}
