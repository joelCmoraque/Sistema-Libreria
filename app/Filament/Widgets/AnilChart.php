<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class AnilChart extends ChartWidget
{
    use InteractsWithPageFilters;
    protected static ?string $heading = 'Distribución de ventas por proveedor';
    protected static bool $isLazy = false;
    protected static ?int $sort = 1;

    protected function getData(): array
    {
        // Obtener el filtro de proveedor del Dashboard
        $providerFilter = $this->filters['provider_filter'] ?? null;

        // Consulta para obtener las ventas totales por proveedor
        $query = DB::connection('pgsql_second')
            ->table('fact_transactions as f')
            ->join('dim_products as p', 'f.product_key', '=', 'p.product_key')
            ->select('p.provider_nombre', DB::raw('SUM(f.total_price) as total_ventas'))
            ->where('f.transaction_type', 'output');

        // Aplicar filtro de proveedor si se ha seleccionado
        if (!empty($providerFilter)) {
            $query->whereIn('p.provider_id', $providerFilter);
        }

        $results = $query->groupBy('p.provider_nombre')->get();

        // Formatear los datos para el gráfico
        $labels = [];
        $sales = [];

        foreach ($results as $result) {
            $labels[] = $result->provider_nombre;  // Nombre del proveedor
            $sales[] = $result->total_ventas;  // Total de ventas por proveedor
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ventas por Proveedor',
                    'data' => $sales,  // Valores para cada proveedor
                    'backgroundColor' => [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                    ],  // Colores para cada proveedor
                ],
            ],
            'labels' => $labels,  // Nombres de los proveedores
        ];
    }


    protected function getType(): string
    {
        return 'doughnut';
    }

  
}
