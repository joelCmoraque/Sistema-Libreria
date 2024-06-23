<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Output;
use Carbon\Carbon;

class LineChart extends ChartWidget
{
    protected static ?string $heading = 'Productos con Más Salidas por Mes';
    protected static ?int $sort = 2;
    protected static string $color = 'success';

    protected function getData(): array
    {
        $data = $this->getProductsPerMonth();
        
        // Preparar los datos para el dataset
        $datasets = [
            [
                'label' => 'Productos con Más Salidas',
                'data' => $data['productsWithMostOutputs'],
             
            ],
        ];

        return [
            'datasets' => $datasets,
            'labels' => $data['months'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getProductsPerMonth(): array
    {
        $now = Carbon::now();
        $productsWithMostOutputs = [];
        $productNames = [];

        // Nombres de los meses en español
        $meses = [
            'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
            'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
        ];

        // Iterar sobre los meses del año
        $months = collect(range(1, 12))->map(function ($month) use ($now, &$productsWithMostOutputs, &$productNames, $meses) {
            // Obtener el producto con más salidas en el mes actual
            $product = Output::whereMonth('created_at', Carbon::parse($now->month($month)->format('Y-m')))
                             ->orderByDesc('cantidad') // Ordenar por la cantidad de salidas
                             ->first();

            if ($product) {
                // Guardar la cantidad de salidas del producto
                $productsWithMostOutputs[] = $product->cantidad;
                // Guardar el nombre del producto
                $productNames[] = $product->nombre;
            } else {
                $productsWithMostOutputs[] = 0; // Si no hay productos, poner cantidad 0
                $productNames[] = ''; // Nombre vacío si no hay productos
            }

            // Devolver el nombre del mes en español
            return $meses[$month - 1]; // Restamos 1 porque los meses en PHP se indexan desde 0
        })->toArray(); // Convertir la colección resultante en un arreglo

        // Devolver un arreglo asociativo con las cantidades de productos con más salidas por mes, nombres de productos y nombres de los meses en español
        return [
            'productsWithMostOutputs' => $productsWithMostOutputs,
            'productNames' => $productNames,
            'months' => $months,
        ];
    }
}