<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AnilChart extends ChartWidget
{
    protected static ?string $heading = 'Productos más Económicos';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Consulta para obtener los 5 productos más económicos
        $productData = DB::table('products')
            ->select('nombre', 'precio_actual')
            ->orderBy('precio_actual', 'asc')
            ->limit(5)
            ->get();

        // Preparar los datos para el gráfico
        $labels = [];
        $prices = [];
        $colors = ['rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)']; // Colores personalizados

        foreach ($productData as $data) {
            $labels[] = $data->nombre;
            $prices[] = $data->precio_actual;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Precio de productos',
                    'data' => $prices,
                    'backgroundColor' => $colors, // Colores de los resultados
                    'borderColor' => [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

  
}
