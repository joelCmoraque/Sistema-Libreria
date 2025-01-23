<?php

namespace App\Livewires;

use App\Models\Outputs;
use App\Models\Input;
use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms;
use Closure;
use Filament\Forms\Components\KeyValue;
use Exception;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\Action as FilamentAction;

class Salidass extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public function mount()
    {
        if (Gate::denies('viewAny', Output::class)) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
    }

    public static function updateTotals()
    {
        // Retrieve all selected products and remove empty rows
        $selectedProducts = collect($get('product'))->filter(fn ($item) => !empty($item['product_id']) && !empty($item['cantidad']));

        // Retrieve prices for all selected products
        $prices = Product::find($selectedProducts->pluck('product_id'))->pluck('precio_actual', 'id');

        // Calculate subtotal based on the selected products and quantities
        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) use ($prices) {
            return $subtotal + ($prices[$product['product_id']] * $product['cantidad']);
        }, 0);

        // Update the state with the new values
        $set('subtotal', number_format($subtotal, 2, '.', ''));
        $set('total', number_format($subtotal + ($subtotal * ($get('iva') / 100)), 2, '.', ''));
    }

    public $cantidad;
    public $product_id;

    public function validateQuantity($productId, $cantidad)
    {
        $product = Product::find($productId);
        if ($product && $cantidad > $product->stock_actual) {
            throw new \Exception("La cantidad ingresada es superior al stock actual ({$product->stock_actual}).");
        }
        return true;
    }

    public function render()
    {
        return view('livewire.salidas', [
            'products' => Product::all(),
        ]);
    }



    public function table(Table $table): Table
    {
        $products = Product::get();
        return $table
            ->query(Output::query())
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.nombre')->label('producto')->searchable()->numeric()->sortable(),

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

            ->filters([
                SelectFilter::make('product_id')
                    ->relationship('product', 'nombre'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive(),
                        Forms\Components\TextInput::make('cantidad')
                            ->required()
                            ->numeric()
                            ->live()
                            ->rule(function (Forms\Get $get): Closure {
                                return function (string $attribute, $value, Closure $fail) use ($get) {
                                    try {
                                        $this->validateQuantity($get('product_id'), $value);
                                    } catch (\Exception $e) {
                                        $fail($e->getMessage());
                                    }
                                };
                            })
                            ->afterStateUpdated(function (Forms\Components\TextInput $component) {
                                $this->validateOnly($component->getStatePath());
                            }),
                        Forms\Components\DateTimePicker::make('fecha')
                            ->required(),

                        Forms\Components\TextInput::make('documento_referencia')
                            ->maxLength(255),
                    ])->authorize(fn (Output $record) => Gate::allows('update', $record)),
                Tables\Actions\DeleteAction::make()
                    ->authorize(fn (Output $record) => Gate::allows('delete', $record)),
            ])
            ->headerActions([

                Tables\Actions\CreateAction::make()
                    ->modalHeading('Crear Nuevo')
                    ->label('Crear Nuevo')
                    ->model(Output::class)

                    ->form([
                        Forms\Components\Grid::make(2) // Crear una cuadrícula con 2 columnas
                            ->schema([


                                Forms\Components\Repeater::make('product')
                                    ->schema([
                                        Forms\Components\Select::make('product_id')
                                            ->relationship('product', 'nombre')
                                            ->options(
                                                $products->mapWithKeys(function (Product $product) {
                                                    return [$product->id => sprintf('%s ($%s)', $product->nombre, $product->precio_actual)];
                                                })
                                            )
                                            ->disableOptionWhen(function ($value, $state, Get $get) {
                                                return collect($get('../*.product_id'))
                                                    ->reject(fn ($id) => $id == $state)
                                                    ->filter()
                                                    ->contains($value);
                                            })
                                            ->required(),
                                        Forms\Components\TextInput::make('cantidad')
                                            ->integer()
                                            ->default(1)
                                            ->required()
                                    ])
                                    // Repeatable field is live so that it will trigger the state update on each change
                                    ->live()
                                    // After adding a new row, we need to update the totals
                                    ->afterStateUpdated(function (App\Livewire\Get $get, App\Livewire\Set $set) {
                                        self::updateTotals($get, $set);
                                    })
                                    // After deleting a row, we need to update the totals
                                    ->deleteAction(
                                        fn (Action $action) => $action->after(fn (Forms\Get $get, Forms\Set $set) => self::updateTotals($get, $set)),
                                    )
                                    // Disable reordering
                                    ->reorderable(false)
                                    ->columns(2)
                            ]),
                        Section::make()
                            ->columns(1)
                            ->maxWidth('1/2')
                            ->schema([
                                Forms\Components\TextInput::make('subtotal')
                                    ->numeric()
                                    // Read-only, because it's calculated
                                    ->readOnly()
                                    ->prefix('$')
                                    // This enables us to display the subtotal on the edit page load
                                    ->afterStateHydrated(function (Forms\Get $get, Forms\Set $set) {
                                        self::updateTotals($get, $set);
                                    }),
                                Forms\Components\TextInput::make('iva')
                                    ->suffix('%')
                                    ->required()
                                    ->numeric()
                                    ->default(20)
                                    // Live field, as we need to re-calculate the total on each change
                                    ->live(true)
                                    // This enables us to display the subtotal on the edit page load
                                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                        self::updateTotals($get, $set);
                                    }),
                                Forms\Components\TextInput::make('total')
                                    ->numeric()
                                    // Read-only, because it's calculated
                                    ->readOnly()
                                    ->prefix('$')


                            ]),
                    ])

                    ->authorize(fn () => Gate::allows('create', Output::class)),
            ]);
    }
}
