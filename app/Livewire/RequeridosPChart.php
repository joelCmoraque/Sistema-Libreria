<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class RequeridosPChart extends ChartWidget
{
    use InteractsWithPageFilters;
    protected static ?string $heading = 'Mayores Salidas de Productos';
    protected static string $color = 'success';
    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        $productData = DB::table('outputs')
        ->join('products', 'outputs.product_id', '=', 'products.id')
        ->select(DB::raw('products.nombre as product_name, COUNT(outputs.id) as quantity'))
            ->when($startDate, fn (Builder $query) => $query->whereDate('outputs.fecha_salida', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('outputs.fecha_salida', '<=', $endDate))
            ->groupBy('products.nombre')
            ->get();

        // Preparar los datos para el gráfico
        $labels = [];
        $quantities = [];

        foreach ($productData as $data) {
            $labels[] = $data->product_name;
            $quantities[] = $data->quantity;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Cantidad de salidas',
                    'data' => $quantities,
                    'backgroundColor' => '#0277BD',
                    'borderColor' => '#29B6F6',
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
