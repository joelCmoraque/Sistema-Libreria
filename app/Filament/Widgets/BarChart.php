<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Product;
use Illuminate\Support\Facades\DB;


class BarChart extends ChartWidget
{
    protected static ?string $heading = 'Categorias con más Productos';
    protected static ?int $sort = 2;
    protected static string $color = 'primary';



    protected function getData(): array
    {
        $productData = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(DB::raw('categories.nombre as category_name, COUNT(products.id) as quantity'))
            ->groupBy('categories.nombre')
            ->get();

        // Preparar los datos para el gráfico
        $labels = [];
        $quantities = [];

        foreach ($productData as $data) {
            $labels[] = $data->category_name;
            $quantities[] = $data->quantity;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad de productos',
                    'data' => $quantities,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
