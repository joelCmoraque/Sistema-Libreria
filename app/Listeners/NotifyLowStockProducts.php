<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Product;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class NotifyLowStockProducts
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        //
        $user = $event->user;

        // Obtener todos los productos con stock por debajo del óptimo
        $lowStockProducts = Product::where('stock_actual', '<', 20)->get();

        if ($lowStockProducts->isNotEmpty()) {
            $totalLowStockProducts = $lowStockProducts->count();

            Notification::make()
                ->title('Productos con stock bajo')
                ->body('Hay un total de '. $totalLowStockProducts .' productos con stock por debajo del óptimo')
                ->actions([
                    Action::make('Revisar')
                    ->button()
                    ->url(route('stock-critico'))
                ])
                ->sendToDatabase($user); // Enviar notificación a la base de datos del usuario autenticado
        }
    }
}
