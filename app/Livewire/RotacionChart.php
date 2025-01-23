<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RotacionChart extends ChartWidget
{
    protected static ?string $heading = 'Rotación Inventario';
    protected static ?int $sort = 0;
    protected static string $color = 'success';
    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];
    protected static bool $isLazy = false;

    protected function getData(): array
    {
       // Obtener datos históricos desde la base de datos
       $resultados = DB::table('products as p')
       ->join('inputs as e', 'p.id', '=', 'e.product_id')
       ->join('outputs as s', 'p.id', '=', 's.product_id')
       ->select(
           DB::raw('TO_CHAR(e.fecha_entrada, \'YYYY-MM\') as mes'),
           DB::raw('SUM(e.cantidad) as cantidad_entradas'),
           DB::raw('SUM(s.cantidad) as cantidad_salidas')
       )
       ->groupBy(DB::raw('TO_CHAR(e.fecha_entrada, \'YYYY-MM\')'))
       ->orderBy(DB::raw('TO_CHAR(e.fecha_entrada, \'YYYY-MM\')'))
       ->get();

   // Formatear los datos para el gráfico
   $data = [
       'datasets' => [
           [
               'label' => 'Cantidad de Entradas',
               'data' => $resultados->map(function ($item) {
                   return [
                       'x' => $item->mes,
                       'y' => $item->cantidad_entradas,
                   ];
               })->toArray(),
               'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
               'borderColor' => 'rgba(54, 162, 235, 1)',
               'borderWidth' => 1,
           ],
           [
               'label' => 'Cantidad de Salidas',
               'data' => $resultados->map(function ($item) {
                   return [
                       'x' => $item->mes,
                       'y' => $item->cantidad_salidas,
                   ];
               })->toArray(),
               'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
               'borderColor' => 'rgba(255, 99, 132, 1)',
               'borderWidth' => 1,
           ],
       ],
       'options' => [
           'scales' => [
               'x' => [
                   'title' => [
                       'display' => true,
                       'text' => 'Mes',
                   ],
                   'type' => 'time',
                   'time' => [
                       'unit' => 'month',
                   ],
               ],
               'y' => [
                   'title' => [
                       'display' => true,
                       'text' => 'Cantidad',
                   ],
               ],
           ],
       ],
   ];
       // Formatear los datos para el gráfico
       $labels = $resultados->pluck('mes')->toArray();
       $entradas = $resultados->pluck('cantidad_entradas')->toArray();
       $salidas = $resultados->pluck('cantidad_salidas')->toArray();


   return [
    'labels' => $labels,
    'datasets' => [
        [
            'label' => 'Cantidad de Entradas',
            'data' => $entradas,
            'borderColor' => '#36A2EB',
            'backgroundColor' => '#9BD0F5',
        ],
        [
            'label' => 'Cantidad de Salidas',
            'data' => $salidas,
            'borderColor' => '#FF6384',
            'backgroundColor' => '#FFB1C1',
        ],
    ],
];
    }

    protected function getType(): string
    {
        return 'scatter';
    }
}
