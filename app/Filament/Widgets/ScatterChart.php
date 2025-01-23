<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Actions\Action;

class ScatterChart extends ChartWidget
{
    use InteractsWithPageFilters;
    protected static ?int $sort = 0;
    protected static string $color = 'warning';
    protected static bool $isLazy = false;  protected static ?string $heading = 'Relación entre Precio Unitario y Cantidad de Salidas';
    
    protected function getData(): array
    {
        // Obtener los filtros seleccionados desde el Dashboard
        $categoryFilter = $this->filters['product_category'] ?? null;
        $dateRangeFilter = $this->filters['date_range'] ?? 'last_30_days';

        // Inicializar el nombre de la categoría
    $categoryName = 'Productos';  // Valor por defecto si no se selecciona ninguna categoría

    // Si hay un filtro de categoría, obtener el nombre de la categoría
    if (!empty($categoryFilter)) {
        $category = DB::connection('pgsql_second')->table('dim_products')
            ->where('category_id', '=', $categoryFilter)
            ->select('category_nombre')
            ->first();

        if ($category) {
            $categoryName = $category->category_nombre;  // Asignar el nombre de la categoría seleccionada
        }
    }

        // Construir la consulta a la base de datos
        $query = DB::connection('pgsql_second')->table('fact_transactions as f')
            ->join('dim_products as p', 'f.product_key', '=', 'p.product_key')
            ->select('p.nombre as product_name', 'f.unit_price', 'f.quantity')
            ->where('f.transaction_type', 'output');

        // Aplicar el filtro de categoría, si está seleccionado
        if (!empty($categoryFilter)) {
            $query->where('p.category_id', '=', $categoryFilter);
        }

        // Aplicar el filtro de rango de fechas
        if ($dateRangeFilter === 'last_30_days') {
            $query->where('f.created_at', '>=', now()->subDays(30));
        } elseif ($dateRangeFilter === 'last_6_months') {
            $query->where('f.created_at', '>=', now()->subMonths(6));
        } elseif ($dateRangeFilter === 'last_year') {
            $query->where('f.created_at', '>=', now()->subYear());
        }

        // Ejecutar la consulta
        $transactions = $query->get();

        // Formatear los datos para el gráfico
        $data = $transactions->map(function ($transaction) {
            return [
                'x' => $transaction->unit_price,
                'y' => $transaction->quantity,
                'label' => $transaction->product_name,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => $categoryName,
                    'data' => $data,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgb(255, 128, 0)',
                    'borderWidth' => 1,
                    'pointRadius' => 3.3,
                    'pointHoverRadius' => 5,
                ],
            ],
        ];
    }

 
    

    protected function getType(): string
    {
        return 'scatter';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            return [
                                "Producto: " + context.raw.label,
                                "Precio Unitario: $" + context.parsed.x.toFixed(2),
                                "Cantidad de Salidas: " + context.parsed.y
                            ];
                        }',
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'type' => 'linear',
                    'position' => 'bottom',
                    'title' => [
                        'display' => true,
                        'text' => 'Precio Unitario de Productos',
                    ],
                ],
                'y' => [
                    'type' => 'linear',
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Cantidad de Salidas',
                    ],
                ],
            ],
        ];
    }
    

}
