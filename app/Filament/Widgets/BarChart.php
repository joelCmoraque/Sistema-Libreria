<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Actions\Action;

class BarChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = -1;
    protected static string $color = 'warning';
    protected static bool $isLazy = false;

     public function getHeading(): string
    {
        $topType = $this->filters['topType'] ?? 'mayor';
        return $topType === 'mayor' 
            ? 'Top 10 Marcas con Mayor Rotación'
            : 'Top 10 Marcas con Menor Rotación';
    }

    protected function filterFormData($data): array
    {
        $this->heading = $this->getHeading();
        return $data;
    }

   
    public function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->label('Editar')
                ->icon('heroicon-o-pencil')
                ->action(function () {
                    // Aquí va la lógica cuando se hace clic en el botón
                    $this->dispatchBrowserEvent('notify', 'Botón clickeado!');
                })
        ];
    }

    protected function getTopSalidas($order, $limit, $filterColumn, $filterValue)
    {
        return DB::connection('pgsql_second')->table('fact_transactions')
            ->join('dim_products', 'fact_transactions.product_key', '=', 'dim_products.product_key')
            ->when($filterValue, fn (Builder $query) => $query->where('fact_transactions.quantity', $filterColumn, $filterValue))
            ->where('fact_transactions.transaction_type', 'output')
            ->select('dim_products.brand_nombre as nombre_marca', DB::raw('SUM(fact_transactions.quantity) as cantidad_salidas'))
            ->groupBy('dim_products.brand_nombre')
            ->orderBy('cantidad_salidas', $order)
            ->limit($limit)
            ->get();
    }

    protected function getData(): array
    {
        $mayor = $this->filters['mayor'] ?? null;
        $menor = $this->filters['menor'] ?? null;
        $topType = $this->filters['topType'] ?? 'mayor'; // 'mayor' o 'menor'

        // Determinar si obtener top mayores o menores según el filtro 'topType'
        if ($topType === 'mayor') {
            $brandData = $this->getTopSalidas('desc', 10, '>=', $mayor);
        } else {
            $brandData = $this->getTopSalidas('asc', 10, '<=', $menor);
        }

        $labels = [];
        $quantities = [];

        foreach ($brandData as $data) {
            $labels[] = $data->nombre_marca;
            $quantities[] = $data->cantidad_salidas;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Frecuencia de Salidas',
                    'data' => $quantities,
                    'backgroundColor' => '#0277BD',
                    'borderColor' => '#29B6F6',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
       
 
        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'TasksChart',
                    'data' => 'data',
                ],
            ],
            'xaxis' => [
                'categories' => 'cat',
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'colors' => '#9ca3af',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'colors' => ['#6366f1'],
            'stroke' => [
                'curve' => 'smooth',
            ],
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('date_start')
                ->default(now()->subMonth()),
            DatePicker::make('date_end')
                ->default(now()),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
