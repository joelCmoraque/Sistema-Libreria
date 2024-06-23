<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\Provider;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '1s';

    
    protected function getStats(): array
    {

        $loginTime = session('login_time');
        $activeSessionTime = 'N/A';
    
        if ($loginTime) {
            // Calcular el tiempo de sesión activa
            $loginTime = Carbon::parse($loginTime);
            $currentTime = Carbon::now();
            $activeSessionTime = $loginTime->diff($currentTime)->format('%H:%I:%S');
        }
        return [
            //
            Stat::make('Cantidad Total de Productos', Product::count())
            ->description('Total:')
            ->descriptionIcon('heroicon-o-Rectangle-Group')
            ->color('success'),
            Stat::make('Cantidad Total de Proveedores', Provider::count())
            ->description('Total:')
            ->descriptionIcon('heroicon-o-truck')
            ->color('info'),
            Stat::make('Tiempo Sesión Activa', $activeSessionTime),

        ];
    }

    
}
