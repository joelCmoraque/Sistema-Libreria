<?php

namespace App\Livewire;

use App\Models\Input;

use App\Models\Product;

use App\Models\Provider;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class Entradas extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public $compra_unitaria;
    public $provider_id;
    public $product_id;
    public $iva = 0;
    public $costo_unitario = 0;
    public $tableSearch = '';
    public $filters = [
        'product_id' => '',
    ];






    public function mount()
    {
        if (Gate::denies('viewAny', Input::class)) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
    }

    public $cantidad;

    public function validateQuantity($cantidad)
    {

        if ($cantidad < 1) {
            throw new \Exception("La cantidad ingresada debe ser mayor o igual a 1.");
        }
        if ($cantidad > 500) {
            throw new \Exception("La cantidad ingresada debe ser menor o igual a 500.");
        }
        return true;
    }


    public $cantidadO;
    public $cantidad_restante;

    protected $listeners = ['state-updated'];

    public function stateUpdated($field, $value)
    {
        if ($field === 'cantidad') {
            $this->cantidad_restante = $value;
        }
    }

    public function render()
    {

        $products = Product::query()
            ->when($this->provider_id, function ($query) {
                $query->where('provider_id', $this->provider_id);
            })
            ->when($this->product_id, function ($query) {
                $query->where('id', $this->product_id);
            })
            ->get();

        return view('livewire.entradas', [
            'products' => $products,
            'providers' => Provider::all(),
        ]);
    }

    public function validatePrecioMinimo($compra_unitaria)
    {
        if ($compra_unitaria < 0.10) {
            throw new \Exception("El valor ingresado debe ser mayor igual a 0.10.");
        }
        return true;
    }



    public function generatePdf()
    {

        ini_set('memory_limit', '256M');
        set_time_limit(300);
        $filters = $this->tableFilters;
        $searchTerm = $this->tableSearch;
        $searchTerm = mb_strtolower($searchTerm, 'UTF-8');

      

        // Construir la consulta con los filtros
        $query = Input::query();

        // Aplicar filtro de término de búsqueda
        $query->whereHas('product.brand', function ($query) use ($searchTerm) {
            $query->whereRaw('LOWER(nombre) LIKE ?', ['%' . $searchTerm . '%']);
        })
            ->orWhereHas('product', function ($query) use ($searchTerm) {
                $query->whereRaw('LOWER(nombre) LIKE ?', ['%' . $searchTerm . '%']);
            });

        // Aplicar otros filtros si existen
        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        // Ejecutar la consulta y obtener los registros
        $records = $query->get();

        $dateTime = now()->format('d-m-Y H:i:s');

        $totalCantidad = $records->sum('cantidad');

        if ($records->isNotEmpty()) {
            $pdf = PDF::loadView('livewire.report', ['records' => $records, 'totalCantidad' => $totalCantidad, 'filters' => $filters, 'dateTime' => $dateTime]);

            return response()->streamDownload(
                fn () => print($pdf->output()),
                'reporte_salidas.pdf'
            );
        } else {
            return redirect()->back()->with('error', 'No se encontraron registros');
        }
    }

    public function message()
    {
        return 'La cantidad debe ser un número mayor o igual a 0.'; // Su mensaje personalizado aquí
    }




    public function table(Table $table): Table
    {


        return $table
            ->query(
                Input::query()
                 
            )
            ->columns([
                Tables\Columns\TextColumn::make('product.nombre')
                    ->label('Producto')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.brand.nombre')
                    ->label('Marca')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.provider.razon_social')
                    ->label('Proveedor')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('cantidad')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('cantidad_restante')
                    ->label('Restante')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_entrada')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('compra_unitaria')
                    ->label('Compra Unitaria')
                    ->getStateUsing(fn ($record) => $record->compra_unitaria . ' bs')
                    ->sortable(),
                Tables\Columns\TextColumn::make('iva')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('costo_unitario')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('documento_referencia')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            //FILTROS PARA APLICAR
            ->filters([
          Filter::make('fecha_entrada')
    ->form([
        Forms\Components\DatePicker::make('created_from')
        ->label('ingreso desde'),
        Forms\Components\DatePicker::make('created_until')
        ->label('ingreso hasta'),
    ])
    ->query(function (Builder $query, array $data): Builder {
        return $query
            ->when(
                $data['created_from'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
            )
            ->when(
                $data['created_until'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
            );
    })

            ])

            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        Forms\Components\Grid::make(2) // Crear una cuadrícula con 2 columnas
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'nombre')
                                    ->label('Producto')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('cantidad')
                                    ->numeric()
                                    ->required()
                                    ->live() // Hacer que el campo sea reactivo
                                    ->rule(function (Forms\Get $get): Closure {
                                        return function (string $attribute, $value, Closure $fail) use ($get) {
                                            try {
                                                $this->validateQuantity($get('cantidad'), $value);
                                            } catch (\Exception $e) {
                                                $fail($e->getMessage());
                                            }
                                        };
                                    })

                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                        $this->validateOnly($component->getStatePath());
                                    }),
                                Forms\Components\TextInput::make('compra_unitaria')
                                    ->required()
                                    ->numeric()
                                    ->live() // Hacer que el campo sea reactivo
                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                    }),
                                Forms\Components\TextInput::make('documento_referencia')
                                    ->maxLength(255),



                            ]),
                    ])->authorize(fn (Input $record) => Gate::allows('update', $record)),
                //  Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->authorize(fn (Input $record) => Gate::allows('delete', $record))
                    ->modalHeading('Borrar registro de entrada'),


            ])
            ->headerActions([


                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    ->icon('heroicon-o-document')
                    ->authorize(fn () => Gate::allows('generatePdf', Input::class))
                    ->action('generatePdf')
                    ->openUrlInNewTab(),

                Tables\Actions\CreateAction::make()
                    ->modalHeading('Crear Nuevo')
                    ->label('Crear Nuevo')
                    ->model(Input::class)

                    ->form([
                        Forms\Components\Grid::make(2) // Crear una cuadrícula con 2 columnas
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'nombre')
                                    ->label('Producto')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('cantidad')
                                    ->numeric()
                                    ->required()
                                    ->live() // Hacer que el campo sea reactivo
                                    ->rule(function (Forms\Get $get): Closure {
                                        return function (string $attribute, $value, Closure $fail) use ($get) {
                                            try {
                                                $this->validateQuantity($get('cantidad'), $value);
                                            } catch (\Exception $e) {
                                                $fail($e->getMessage());
                                            }
                                        };
                                    })

                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                        $this->validateOnly($component->getStatePath());
                                    }),
                                Forms\Components\TextInput::make('compra_unitaria')
                                    ->required()
                                    ->numeric()
                                    ->live() // Hacer que el campo sea reactivo
                                    ->rule(function (Forms\Get $get): Closure {
                                        return function (string $attribute, $value, Closure $fail) use ($get) {
                                            try {
                                                $this->validatePrecioMinimo($get('compra_unitaria'), $value);
                                            } catch (\Exception $e) {
                                                $fail($e->getMessage());
                                            }
                                        };
                                    })
                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                        $this->validateOnly($component->getStatePath());
                                    }),
                                Forms\Components\TextInput::make('documento_referencia')
                                    ->maxLength(255),



                            ]),
                    ])
                    ->authorize(fn () => Gate::allows('create', Input::class)),
            ])

            ->bulkActions([
                ExportBulkAction::make(),
                Tables\Actions\BulkAction::make('generatePdf')
                 ->label('PDF') 
                 ->color('warning')
                 ->action(function ($records) { $pdf = Pdf::loadView('livewire.reportFilter', ['records' => $records]); return response()->streamDownload(function () use ($pdf) { echo $pdf->output(); }, 'selected-items.pdf'); }) 
                 ->deselectRecordsAfterCompletion(),
            ]);
    }
}
