<?php

namespace App\Filament\Pages;


use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;





class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;
    use HasFiltersAction;

    protected static ?string $navigationGroup = 'Panel Administrativo';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';


    public static function shouldRegisterNavigation(): bool

    {
        return Gate::allows('viewAny', \App\Models\User::class);
    }

  

   

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ejecutarETL')
                ->label('Actualizar datos')
                ->requiresConfirmation() // Para pedir confirmación antes de ejecutar
                ->color('primary')
                ->icon('heroicon-o-arrow-path')

                ->modalHeading('Confirmar ejecución del ETL')
                ->modalDescription('¿Estás seguro de que deseas ejecutar el proceso ETL? Esta acción no se puede deshacer.')
                ->modalSubmitActionLabel('Sí, ejecutar ETL')
                ->modalCancelActionLabel('Cancelar')
                ->action(function () {
                    $this->ejecutarETL();
                }),

            FilterAction::make()
                ->label('Gráficos')
                ->modalHeading('Modificar gráficos')
                ->color('primary')
                ->icon('heroicon-o-chart-pie')
                ->form([
                    Section::make('Gráfico de barras')
                        ->schema([
                            Select::make('topType')
                                ->label('Tipo de Top')
                                ->options([
                                    'mayor' => 'Top Mayores',
                                    'menor' => 'Top Menores'
                                ])
                                ->default('mayor')
                                ->reactive(),

                            // Campos adicionales según la selección
                            Select::make('mayor')
                                ->label('Mayor que')
                                ->options([
                                    100 => '100',
                                    500 => '500',
                                    1000 => '1000',
                                ])
                                ->visible(fn(callable $get) => $get('topType') === 'mayor'),

                            Select::make('menor')
                                ->label('Menor que')
                                ->options([
                                    100 => '100',
                                    500 => '500',
                                    1000 => '1000',
                                ])
                                ->visible(fn(callable $get) => $get('topType') === 'menor'),
                        ])
                        ->columns(2)
                        ->collapsed()
                        ->icon('heroicon-o-plus'),

                    Section::make('Gráfico de dispersión')
                        ->schema([
                        Select::make('product_category')
                        ->label('Categoría de Producto')
                        ->options(DB::connection('pgsql_second')->table('dim_products')->pluck('category_nombre', 'category_id'))
                        ->placeholder('Selecciona una categoría'),
                        ])
                        ->columns(1)
                        ->collapsed('heroicon-o-plus')
                        ->icon('heroicon-o-plus'),
                    Section::make('Grafico de pastel')
                        ->schema([
                            Select::make('category_filter')
                            ->label('Filtrar por Categoría de Producto')
                            ->options(DB::connection('pgsql_second')->table('dim_products')->pluck('category_nombre', 'category_id'))
                            ->multiple()  // Permite seleccionar varias categorías
                            ->placeholder('Selecciona una o varias categorías'),
                        ])
                        ->columns(2)
                        ->collapsed('heroicon-o-plus')
                        ->icon('heroicon-o-plus'),
                    Section::make('Grafico de dona')
                    ->schema([
                        Select::make('provider_filter')
                            ->label('Filtrar por Proveedor')
                            ->options(DB::connection('pgsql_second')->table('dim_products')->pluck('provider_nombre', 'provider_id'))
                            ->multiple()  // Permite seleccionar varios proveedores
                            ->placeholder('Selecciona uno o varios proveedores'),
                    ])
                        ->columns(3)
                        ->collapsed('heroicon-o-plus')
                        ->icon('heroicon-o-plus'),
                    Section::make('tabla')
                        ->schema([
                        ])
                        ->columns(3)
                        ->collapsed('heroicon-o-plus')
                        ->icon('heroicon-o-plus'),

                ]),
        ];
    }

    // Este es el método que ejecutará el comando
    public function ejecutarETL(): void
    {
        Artisan::call('app:etl-comand');

        Notification::make()
            ->title('Proceso ETL ejecutado')
            ->success()
            ->send();
    }
}
