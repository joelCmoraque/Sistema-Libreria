<?php

namespace App\Livewire;

use App\Models\Deposit;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms;


class Deposite extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public function mount()
    {
        if (Gate::denies('viewAny', Deposit::class)) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
    }
    public function render()
    {
        return view('livewire.deposite');
    }

    public function table(Table $table): Table
    {
         return $table
         ->query(Deposit::query())
         ->columns([
            Tables\Columns\TextColumn::make('nombre')
            ->searchable(),
        Tables\Columns\TextColumn::make('descripcion')
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
        Forms\Components\TextInput::make('descripcion')
        ->authorize(fn (Deposit $record) => Gate::allows('update', $record))
        ]),
            Tables\Actions\DeleteAction::make()
            ->authorize(fn (Deposit $record) => Gate::allows('delete', $record))
         ])
         ->headerActions([
            
            Tables\Actions\CreateAction::make()
            ->modalHeading('Crear Nuevo')
             ->label('Crear Nuevo')
            ->model(Deposit::class)
            
            ->form([
                Forms\Components\TextInput::make('nombre')
                ->required('completar este campo')
                ->maxLength(255),
            Forms\Components\TextInput::make('descripcion')
            ->label('Descripción')
            ->default('sin descripcion'),
            ])
            ->authorize(fn () => Gate::allows('create', Deposit::class)),
            ]);



    }
}
