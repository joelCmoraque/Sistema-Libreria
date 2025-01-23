<?php

namespace App\Livewire;

use App\Models\Output;
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
use Filament\Tables\Filters\Filter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\TestRunner\TestResult\Collector;

class Salidas extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public function mount()
    {
        if (Gate::denies('viewAny', Output::class)) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
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

    public $cantidadmax;

    public function validateQuantityMax($cantidadmax)
    {

        if ($cantidadmax < 1) {
            throw new \Exception("La cantidad ingresada debe ser mayor a 0");
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
        return $table
            ->query(Output::query())
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.nombre')->label('producto')->searchable()->numeric()->sortable(),
                Tables\Columns\TextColumn::make('cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_salida')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product.brand.nombre')
                    ->label('marca')
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
                    ->modalHeading('Editar registro de salida')
                    ->form([



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
                                            ->afterStateUpdated(function (callable $set, $get) {
                                                $precioUnitario = $get('precio_unitario') ?: 0;
                                                $cantidad = $get('cantidad') ?: 0;
                                                $total = $precioUnitario * $cantidad;
                                                $set('total', $total);
                                            })
                                    ]),

                                Forms\Components\Grid::make(2)

                                    ->schema([
                                        Forms\Components\TextInput::make('cantidad')->required()->numeric()->live()->columnSpan(1)

                                            ->rule(function (Forms\Get $get): Closure {
                                                return function (string $attribute, $value, Closure $fail) use ($get) {
                                                    try {
                                                        $this->validateQuantity($get('product_id'), $value);
                                                        $this->validateQuantityMax($get('cantidad'), $value);
                                                    } catch (\Exception $e) {
                                                        $fail($e->getMessage());
                                                    }
                                                };
                                            })

                                            ->afterStateUpdated(function (callable $set, $get, Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                                $this->validateOnly($component->getStatePath());
                                                $precioUnitario = $get('precio_unitario') ?: 0;
                                                $cantidad = $get('cantidad') ?: 0;
                                                $total = $precioUnitario * $cantidad;
                                                $set('total', $total);
                                            }),
                                        Forms\Components\TextInput::make('total')->required()->readOnly()->numeric()->live(),
                                    ])
                            ])
                    ])->authorize(fn (Output $record) => Gate::allows('update', $record)),
                Tables\Actions\DeleteAction::make()
                    ->authorize(fn (Output $record) => Gate::allows('delete', $record))
                    ->modalHeading('Borrar registro de salida'),
            ])
            ->headerActions([

                Tables\Actions\CreateAction::make()
                    ->authorize(fn () => Gate::allows('create', Output::class))
                    ->modalHeading('REGISTRAR NUEVA SALIDA')
                    ->label('REGISTRAR NUEVA SALIDA')
                    ->model(Output::class)

                    ->form([



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
                                            ->afterStateUpdated(function (callable $set, $get) {
                                                $precioUnitario = $get('precio_unitario') ?: 0;
                                                $cantidad = $get('cantidad') ?: 0;
                                                $total = $precioUnitario * $cantidad;
                                                $set('total', $total);
                                            })
                                    ]),

                                Forms\Components\Grid::make(2)

                                    ->schema([
                                        Forms\Components\TextInput::make('cantidad')->required()->numeric()->live()->columnSpan(1)

                                            ->rule(function (Forms\Get $get): Closure {
                                                return function (string $attribute, $value, Closure $fail) use ($get) {
                                                    try {
                                                        $this->validateQuantity($get('product_id'), $value);
                                                        $this->validateQuantityMax($get('cantidad'), $value);
                                                    } catch (\Exception $e) {
                                                        $fail($e->getMessage());
                                                    }
                                                };
                                            })

                                            ->afterStateUpdated(function (callable $set, $get, Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                                $this->validateOnly($component->getStatePath());
                                                $precioUnitario = $get('precio_unitario') ?: 0;
                                                $cantidad = $get('cantidad') ?: 0;
                                                $total = $precioUnitario * $cantidad;
                                                $set('total', $total);
                                            }),
                                        Forms\Components\TextInput::make('total')->required()->readOnly()->numeric()->live(),
                                    ])
                            ])

                    ])

            ])
            ->bulkActions([
                ExportBulkAction::make(),
                Tables\Actions\BulkAction::make('generatePdf')
                 ->label('Generar PDF') 
                 ->action(function ($records) { $pdf = Pdf::loadView('livewire.reportFilterO', ['records' => $records]); return response()->streamDownload(function () use ($pdf) { echo $pdf->output(); }, 'reporte_salidas.pdf'); }) 
                 ->deselectRecordsAfterCompletion(),
            ]);
    }
}
