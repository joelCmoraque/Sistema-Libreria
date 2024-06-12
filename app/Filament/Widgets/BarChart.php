<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class BarChart extends ChartWidget
{
    protected static ?string $heading = 'Chart 1';
     protected static ?int $sort = 2;
  

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Blog posts created',
                    'data' => [10, 20, 30, 25, 15, 35, 40, 50, 60, 70, 80, 90], // Example data
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)', // Bar color
                    'borderColor' => 'rgba(75, 192, 192, 1)', // Border color
                    'borderWidth' => 1, // Border width
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], // Example labels
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
