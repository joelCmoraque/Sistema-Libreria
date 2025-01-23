<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Output;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth; 
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class StockOutObserver
{
    /**
     * Handle the Output "created" event.
     */
    public function created(Output $output): void
    {
         /** @var \App\Models\User $user */
         $user = Auth::user();

         if ($user->hasRole(['admin', 'encargado'])) {
             $lowStockProducts = Product::where('stock_actual', '<', 20)->get();
 
             if ($lowStockProducts->isNotEmpty()) {
                 // Verificar la última vez que se envió una notificación de bajo stock
                 $lastNotificationSentAt = $user->last_low_stock_notification_sent_at;
 
                 // Comparar con una frecuencia mínima 
                 if (!$lastNotificationSentAt || $lastNotificationSentAt->addDay()->isPast()) {
                     $totalLowStockProducts = $lowStockProducts->count();
 
                     Notification::make()
                         ->title('Productos con stock bajo | salida')
                         ->body('Hay un total de ' . $totalLowStockProducts . ' productos con stock por debajo del óptimo')
                         ->actions([
                             Action::make('Revisar')
                                 ->button()
                                 ->url(route('stock-critico'))
                         ])
                         ->sendToDatabase($user); // Enviar notificación a la base de datos del usuario autenticado
 
                     // Actualizar la marca de tiempo de la última notificación enviada
                     $user->update(['last_low_stock_notification_sent_at' => now()]);
                 }
             }
         }
         if (Gate::denies('viewAny', Output::class)) {
             abort(403, 'No tienes permiso para acceder a esta página.');
         }
    }

    /**
     * Handle the Output "updated" event.
     */
    public function updated(Output $output): void
    {
        //
    }

    /**
     * Handle the Output "deleted" event.
     */
    public function deleted(Output $output): void
    {
        //
    }

    /**
     * Handle the Output "restored" event.
     */
    public function restored(Output $output): void
    {
        //
    }

    /**
     * Handle the Output "force deleted" event.
     */
    public function forceDeleted(Output $output): void
    {
        //
    }
}
