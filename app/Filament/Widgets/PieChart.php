<?php

namespace App\Filament\Widgets;


use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PieChart extends ChartWidget
{
    protected static ?string $heading = 'Productos más Vendidos';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Consulta para obtener los 5 productos más vendidos
        $productData = DB::table('outputs')
            ->join('products', 'outputs.product_id', '=', 'products.id')
            ->select(DB::raw('products.nombre, SUM(outputs.cantidad) as total_sales'))
            ->groupBy('products.nombre')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();

        // Preparar los datos para el gráfico
        $labels = [];
        $sales = [];
        $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']; // Colores personalizados

        foreach ($productData as $data) {
            $labels[] = $data->nombre;
            $sales[] = $data->total_sales;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad de ventas',
                    'data' => $sales,
                    'backgroundColor' => $colors, // Colores de los resultados
                ],
            ],
            'labels' => $labels,
        ];
    }


    protected function getType(): string
    {
        return 'pie';
    }
}
