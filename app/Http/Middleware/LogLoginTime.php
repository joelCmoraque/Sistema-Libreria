<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Models\Product;

class LogLoginTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->route()->getName() !== 'logout' && Auth::check() && !session()->has('login_time')) {
            session(['login_time' => now()]);
        }

        // Ejecutar la lógica de notificación antes de devolver la respuesta
        if (Auth::check() && !session()->has('low_stock_notified')) {
            $user = Auth::user();
            $lowStockProducts = Product::where('stock_actual', '<', 20)->get();

            if ($lowStockProducts->isNotEmpty()) {
                $totalLowStockProducts = $lowStockProducts->count();

                Notification::make()
                    ->title('Productos con stock bajo')
                    ->body('Hay un total de ' . $totalLowStockProducts . ' productos con stock por debajo del óptimo')
                    ->actions([
                        Action::make('Revisar')
                        ->button()
                        ->url(route('stock-critico'))
                    ])
                    ->sendToDatabase($user); // Enviar notificación a la base de datos del usuario autenticado
                    session(['low_stock_notified' => true]);
            }
        }

        return $next($request);
    }
}
