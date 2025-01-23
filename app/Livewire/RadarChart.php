<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RadarChart extends ChartWidget
{
    protected static ?string $heading = 'Comparativa de Depósitos por Productos (costo total - ventas realizadas)';
    protected static string $color = 'success';
    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        // Consulta para obtener los datos agregados por depósito
        $depositData = DB::connection('pgsql_second')
        ->table('fact_transactions as f')
        ->join('dim_deposits as d', 'f.deposit_key', '=', 'd.deposit_key')
        ->select(
            'd.nombre as deposit_name',
            DB::raw('SUM(f.quantity) as total_cantidad'),
            DB::raw('SUM(f.total_price) as total_ventas'),
            DB::raw('SUM(f.quantity * f.unit_price) as total_costo'),
            DB::raw('COUNT(DISTINCT f.product_key) as total_productos_distintos')
        )
        ->groupBy('d.nombre')
        ->get();

        // Ejes del radar, que serán los nombres de los depósitos
        $labels = $depositData->pluck('deposit_name')->toArray();

        // Series de datos para el radar
        $datasets = [
            [
                'label' => 'Cantidad en Stock',
                'data' => $depositData->pluck('total_cantidad')->toArray(),
                'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                'borderColor' => 'rgba(54, 162, 235, 1)',
                'pointLabelFontSize' => 16,
            ],
            [
                'label' => 'Total Ventas',
                'data' => $depositData->pluck('total_ventas')->toArray(),
                'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'pointLabelFontSize' => 20,
            ],
            [
                'label' => 'Costo Total',
                'data' => $depositData->pluck('total_costo')->toArray(),
                'backgroundColor' => 'rgba(255, 206, 86, 0.2)',
                'borderColor' => 'rgba(255, 206, 86, 1)',
            ],
            [
                'label' => 'Variedad de Productos',
                'data' => $depositData->pluck('total_productos_distintos')->toArray(),
                'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
                'borderColor' => 'rgba(153, 102, 255, 1)',
            ],
        ];

        return [
            'labels' => $labels,  // Aquí las puntas del radar son los nombres de los depósitos
            'datasets' => $datasets,
        ];
    }
    

    protected function getType(): string
    {
        return 'radar';
    }

   
}
