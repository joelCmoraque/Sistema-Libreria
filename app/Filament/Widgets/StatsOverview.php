<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        return [
            //
            Stat::make('Total de stock', '231.1k'),
            Stat::make('Salida de Productos', '21%'),
            Stat::make('Tiempo Sesión Activa', '01:15 '),

        ];
    }
}
