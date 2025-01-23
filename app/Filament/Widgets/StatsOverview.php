<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Brand;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = -2;
    protected static ?string $pollingInterval = '1s';
    protected static bool $isLazy = false;

    
    protected function getStats(): array
    {

        $loginTime = session('login_time');
        $activeSessionTime = 'N/A';
    
        if ($loginTime) {
            // Calcular el tiempo de sesión activo
            $loginTime = Carbon::parse($loginTime);
            $currentTime = Carbon::now();
            $activeSessionTime = $loginTime->diff($currentTime)->format('%H:%I:%S');
        }
        return [
            //
            Stat::make('Cantidad de Productos', Product::count())
            ->description('Total:')
            ->descriptionIcon('heroicon-o-Rectangle-Group')
            ->color('success'),
            Stat::make('Cantidad de Proveedores', Provider::count())
            ->description('Total:')
            ->descriptionIcon('heroicon-o-truck')
            ->color('info'),
            Stat::make('Cantidad de Marcas', Brand::count())
            ->description('Total:')
            ->descriptionIcon('heroicon-o-Rectangle-Group')
            ->color('warning'),
            Stat::make('Tiempo Sesión Activa', $activeSessionTime),

        ];
    }

    
}
