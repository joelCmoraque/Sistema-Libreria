<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class BlogPostsChart extends ChartWidget
{
    protected static ?string $heading = 'Chart 2';
    protected static ?int $sort = 3;
  

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Blog posts by category',
                    'data' => [10, 20, 30, 40], // Example data
                    'backgroundColor' => [
                        '#FF6384', // Color for the first slice
                        '#36A2EB', // Color for the second slice
                        '#FFCE56', // Color for the third slice
                        '#4BC0C0'  // Color for the fourth slice
                    ],
                ],
            ],
            'labels' => ['Tech', 'Health', 'Lifestyle', 'Education'], // Example labels
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
