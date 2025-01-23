<?php

namespace App\Livewire;

use Filament\Widgets\ChartWidget;
use App\Models\Output;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HistoricChart extends ChartWidget
{
    protected static ?string $heading = 'Histórico de salidas';
    protected static ?int $sort = 0;
    protected static string $color = 'success';
    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        // Obtener los totales de salidas por mes
        $data = $this->getTotalOutputsPerMonth();

        // Preparar los datasets para el gráfico
        $datasets = [
            [
                'label' => 'Total de Salidas',
                'data' => $data['totalOutputs'],
                'borderColor' => '#36A2EB',
                'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                'fill' => true,
                'tension' => 0.4, // Para hacer las líneas suaves
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

    private function getTotalOutputsPerMonth(): array
    {
        $now = Carbon::now();
        $totalOutputs = [];

        // Nombres de los meses en español
        $meses = [
            'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
            'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
        ];

        // Iterar sobre los meses del año y obtener los totales de salidas por mes
        $months = collect(range(1, 12))->map(function ($month) use ($now, &$totalOutputs, $meses) {
            // Consulta para obtener la cantidad total de salidas por mes
            $totalQuantity = DB::connection('pgsql_second')
                ->table('fact_transactions')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $now->year)
                ->where('transaction_type', 'output') // Solo salidas (ventas)
                ->sum('quantity');

            // Guardar la cantidad total de salidas para el mes
            $totalOutputs[] = $totalQuantity;

            // Devolver el nombre del mes en español
            return $meses[$month - 1]; // Restamos 1 porque los meses en PHP se indexan desde 0
        })->toArray(); // Convertir la colección resultante en un arreglo

        // Devolver un arreglo con los totales de salidas por mes y los nombres de los meses
        return [
            'totalOutputs' => $totalOutputs,
            'months' => $months,
        ];
    }
}
