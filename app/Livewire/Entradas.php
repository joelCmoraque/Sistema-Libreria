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
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider.razon_social')
                    ->numeric()
                    ->sortable(),
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
            ->action(function ($record) {
                if ($record) {
                    return response()->streamDownload(function () use ($record) {
                        echo Pdf::loadView('livewire/report', ['record' => $record])->stream();
                    }, $record->id . '.pdf');
                } else {
                    // Manejar el caso en que $record es null
                    // Puedes regresar una redirección o cualquier otra acción deseada
                    return redirect()->back()->with('error', 'No se encontró el registro');
                }
            }),
       
         ])
         ->headerActions([
            
            Tables\Actions\CreateAction::make()
            ->modalHeading('Crear Nuevo')
             ->label('Crear Nuevo')
            ->model(Input::class)
            
            ->form([
                Forms\Components\Select::make('product_id')
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
