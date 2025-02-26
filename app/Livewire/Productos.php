<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use App\Models\User;
use App\Services\ProductForm;
use Illuminate\Support\Facades\Gate;
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
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\KeyValue;
use Filament\Actions\Imports\Importer;
use App\Models\Provider;
use Illuminate\Validation\Rule;
use Closure;
use EightyNine\ExcelImport\ExcelImportAction;


class Productos extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;


    public function mount()
    {
    }


    public function validatePrecioMinimo($precio_actual)
    {
        if ($precio_actual < 0.10) {
            throw new \Exception("El valor ingresado debe ser mayor igual a 0.10.");
        }
        return true;
    }

    public $nombre;



    public function validarNombre($productoId, $nombre)
    {
        // Verifica si el nombre ya existe en la tabla de productos
        $productoExistente = Product::where('nombre', $productoId)->first();

        if ($productoExistente) {
            throw new \Exception("El nombre de producto ({$productoExistente->nombre}) ya existe ");
        }
        // Verificar la longitud mínima y máxima
        if (strlen($nombre) < 3 || strlen($nombre) > 100) {
            throw new \Exception("El nombre debe tener entre 3 y 100 caracteres.");
        }

        // Verificar que el nombre no esté compuesto solo de símbolos o signos de puntuación
        if (preg_match('/^[\p{P}\p{S}]+$/u', $nombre)) {
            throw new \Exception("El nombre no puede estar compuesto solo de símbolos o signos de puntuación.");
        }

        // Verificar que el nombre tenga más de dos caracteres
        if (strlen($nombre) <= 2) {
            throw new \Exception("El nombre debe tener más de dos caracteres.");
        }
        if (empty($nombre)) {
            throw new \Exception('El campo :attribute es obligatorio.');
        }


        return true;
    }







    public $value;
    public $exists;

    public $cantidad;
    public $product_id;


    public function validateQuantity($cantidad)
    {

        if ($cantidad < 0) {
            throw new \Exception("La cantidad ingresada debe ser mayor o igual a 0.");
        }
        if ($cantidad > 500) {
            throw new \Exception("La cantidad ingresada debe ser menor o igual a 500.");
        }
        return true;
    }
    public function generatePdf()
    {

        ini_set('memory_limit', '256M');
        set_time_limit(300);
        $searchTerm = $this->tableSearch;
        $searchTerm = mb_strtolower($searchTerm, 'UTF-8');

        $records = Product::whereHas('brand', function ($query) use ($searchTerm) {
            $query->whereRaw('LOWER(nombre) LIKE ?', ['%' . $searchTerm . '%']);
        })
            ->orWhereHas('category', function ($query) use ($searchTerm) {
                $query->whereRaw('LOWER(nombre) LIKE ?', ['%' . $searchTerm . '%']);
            })
            ->orWhereHas('provider', function ($query) use ($searchTerm) {
                $query->whereRaw('LOWER(razon_social) LIKE ?', ['%' . $searchTerm . '%']);
            })
            ->orWhereHas('deposit', function ($query) use ($searchTerm) {
                $query->whereRaw('LOWER(nombre) LIKE ?', ['%' . $searchTerm . '%']);
            })
            ->orWhereRaw('LOWER(nombre) LIKE ?', ['%' . $searchTerm . '%'])
            ->get();

            $dateTime = now()->format('d-m-Y H:i:s');

        if ($records->isNotEmpty()) {
            $pdf = PDF::loadView('livewire.reportProducts', ['records' => $records,   'dateTime' => $dateTime]);

            return response()->streamDownload(
                fn () => print($pdf->output()),
                'reporte_productos.pdf'
            );
        } else {
            return redirect()->back()->with('error', 'No se encontraron registros');
        }
    }



    public function render()
    {


        return view('livewire.productos');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query())
            ->columns([
                Tables\Columns\TextColumn::make('codigo_unico')->label('Codigo')->sortable(),
                Tables\Columns\TextColumn::make('category.nombre')->label('Categoría')->searchable()->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('provider.razon_social')->label('Proveedor')->searchable()->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('deposit.nombre')->label('Depósito')->searchable(),
                Tables\Columns\TextColumn::make('brand.nombre')->label('Marca')->searchable(),
                Tables\Columns\TextColumn::make('nombre')->searchable(),
                Tables\Columns\TextColumn::make('descripcion')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('precio_actual')->sortable()
                    ->getStateUsing(fn ($record) => $record->precio_actual . ' bs'),
                Tables\Columns\TextColumn::make('stock_actual')->sortable()->label('Stock'),
                Tables\Columns\TextColumn::make('unidad_medida')->sortable()->label('U.medida'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
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
                                                    ->unique(ignoreRecord: true)
                                                    ->live(onBlur: true)

                                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                                        $this->validateOnly($component->getStatePath());
                                                    })
                                                    ->columnSpan(['sm' => 2, 'md' => 3, 'lg' => 2, 'full' => true]),
                                                Forms\Components\TextInput::make('unidad_medida')
                                                    ->maxLength(255)
                                                    ->columnSpan(['sm' => 1, 'md' => 1, 'lg' => 1, 'full' => true]),
                                                Forms\Components\TextInput::make('precio_actual')
                                                    ->required()
                                                    ->numeric()->columnSpan(['sm' => 1, 'md' => 1, 'lg' => 1, 'full' => true]),
                                                Forms\Components\TextInput::make('stock_actual')
                                                    ->required()
                                                    ->numeric()->columnSpan(['sm' => 1, 'md' => 1, 'lg' => 1, 'full' => true]),
                                            ])
                                    ]),
                                Forms\Components\Grid::make(2) // Cuadrícula interna con dos columnas para los campos restantes
                                    ->schema([
                                        Forms\Components\TextInput::make('descripcion')
                                            ->label('Descripción')
                                            ->columnSpan('full'), // 'descripcion' ocupa toda la fila
                                    ]),


                            ])
                    ])

                    ->authorize(fn (Product $record) => Gate::allows('update', $record)),
                Tables\Actions\DeleteAction::make()
                    ->authorize(fn (Product $record) => Gate::allows('delete', $record))
                    ->modalHeading('Borrar Producto'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    ->icon('heroicon-o-document')
                    ->authorize(fn () => Gate::allows('generatePdf', Product::class))
                    ->action('generatePdf')
                    ->openUrlInNewTab(),
                Tables\Actions\CreateAction::make()
                    ->modalHeading('Crear Nuevo')
                    ->label('Crear Nuevo')
                    ->model(Product::class)
                    ->form([
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
                                            ->default(1)
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
                                            ->default(1)
                                            ->searchable()
                                            ->preload(),
                                    ]),
                                Forms\Components\Grid::make(1) // Cuadrícula principal con una sola columna para contener las cuadrículas internas
                                    ->schema([
                                        Forms\Components\Grid::make(5) // Cuadrícula interna con cuatro columnas para 'nombre', 'unidad_medida', 'precio_actual' y 'stock_actual'
                                            ->schema([
                                                Forms\Components\TextInput::make('nombre')
                                                    ->required()
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
                                                    ->placeholder('Medida')
                                                    ->searchable()
                                                    ->default('ninguno')
                                                    ->options([
                                                        'pzas' => 'Piezas',
                                                        'pza' => 'Pieza',
                                                        'ud' => 'Unidad',
                                                        'est' => 'Estuche',
                                                        'cj' => 'Caja',
                                                        'cjs' => 'Cajas',
                                                        'blist' => 'Blister',
                                                        'rlls' => 'Rollos',
                                                        'blsa' => 'Bolsa',
                                                        'blsas' => 'Bolsas',
                                                        'hja' => 'Hoja',
                                                        'hjas' => 'Hojas',
                                                        'pqt' => 'Paquetes',
                                                        'pqts' => 'Paquete',
                                                        'blk' => 'Block',
                                                        'kg' => 'Kilos',

                                                    ])
                                                    ->columnSpan(['sm' => 1, 'md' => 1, 'lg' => 1, 'full' => true]),
                                                Forms\Components\TextInput::make('precio_actual')
                                                    ->required()
                                                    ->live()
                                                    ->numeric()->columnSpan(['sm' => 1, 'md' => 1, 'lg' => 1, 'full' => true])
                                                    ->columnSpan(1)
                                                    ->rule(function (Forms\Get $get): Closure {
                                                        return function (string $attribute, $value, Closure $fail) use ($get) {
                                                            try {
                                                                $this->validatePrecioMinimo($get('precio_actual'), $value);
                                                            } catch (\Exception $e) {
                                                                $fail($e->getMessage());
                                                            }
                                                        };
                                                    })
                                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                                        $this->validateOnly($component->getStatePath());
                                                    }),
                                                Forms\Components\TextInput::make('stock_actual')
                                                    ->required()
                                                    ->numeric()->columnSpan(['sm' => 1, 'md' => 1, 'lg' => 1, 'full' => true])
                                                    ->live()
                                                    ->rule(function (Forms\Get $get): Closure {
                                                        return function (string $attribute, $value, Closure $fail) use ($get) {
                                                            try {
                                                                $this->validateQuantity($get('stock_actual'), $value);
                                                            } catch (\Exception $e) {
                                                                $fail($e->getMessage());
                                                            }
                                                        };
                                                    })

                                                    ->afterStateUpdated(function (Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                                        $this->validateOnly($component->getStatePath());
                                                    }),
                                            ])
                                    ]),
                                Forms\Components\Grid::make(2) // Cuadrícula interna con dos columnas para los campos restantes
                                    ->schema([
                                        Forms\Components\TextInput::make('descripcion')
                                            ->label('Descripción')
                                            ->columnSpan('full'), // 'descripcion' ocupa toda la fila
                                    ]),


                            ])
                    ])
                    ->authorize(fn () => Gate::allows('create', Product::class)),

            ])


            ->bulkActions([
                ExportBulkAction::make()
                    ->authorize(fn () => Gate::allows('create', Product::class)),

            ]);
    }
}
