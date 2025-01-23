<?php

namespace App\Filament\Widgets;


use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Filament\Widgets\DateFilterWidget;

class PieChart extends ChartWidget
{

    use InteractsWithPageFilters;
    protected static ?string $heading = 'Distribución de productos por categoría';
    protected static bool $isLazy = false;
    protected static ?int $sort = 1;


    protected function getData(): array
    {
         // Obtener el filtro de categoría del Dashboard
         $categoryFilter = $this->filters['category_filter'] ?? null;

         // Consulta para obtener la cantidad total vendida por categoría
         $query = DB::connection('pgsql_second')
             ->table('fact_transactions as f')
             ->join('dim_products as p', 'f.product_key', '=', 'p.product_key')
             ->select('p.category_nombre', DB::raw('SUM(f.quantity) as total_cantidad'))
             ->where('f.transaction_type', 'output');
 
         // Aplicar filtro de categoría si se ha seleccionado
         if (!empty($categoryFilter)) {
             $query->whereIn('p.category_id', $categoryFilter);
         }
 
         $results = $query->groupBy('p.category_nombre')->get();
 
         // Formatear los datos para el gráfico
         $labels = [];
         $quantities = [];
 
         foreach ($results as $result) {
             $labels[] = $result->category_nombre;  // Nombre de la categoría
             $quantities[] = $result->total_cantidad;  // Total de cantidad vendida
         }
 
         return [
             'datasets' => [
                 [
                     'label' => 'Cantidad Vendida',
                     'data' => $quantities,  // Valores para cada categoría
                     'backgroundColor' => [
                         '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                     ],  // Colores para cada categoría
                 ],
             ],
             'labels' => $labels,  // Etiquetas de las categorías
         ];
     }
 

    protected function getType(): string
    {
        return 'pie';
    }
}
