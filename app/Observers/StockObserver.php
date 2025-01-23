<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Gate;
use App\Models\User; // Asegúrate de importar el modelo User
use Illuminate\Support\Facades\Auth; // Importa el facade Auth
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class StockObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
           /** @var \App\Models\User $user */
           $user = Auth::user();

           if ($user->hasRole(['admin', 'encargado'])) {
               $lowStockProducts = Product::where('stock_actual', '<', 20)->get();
   
               if ($lowStockProducts->isNotEmpty()) {
                   // Verificar la última vez que se envió una notificación de bajo stock
                   $lastNotificationSentAt = $user->last_low_stock_notification_sent_at;
   
                   // Comparar con una frecuencia mínima (por ejemplo, una vez al día)
                   if (!$lastNotificationSentAt || $lastNotificationSentAt->addDay()->isPast()) {
                       $totalLowStockProducts = $lowStockProducts->count();
   
                       Notification::make()
                           ->title('Productos con stock bajo')
                           ->body('Hay un total de ' . $totalLowStockProducts . ' productos con stock por debajo del óptimo56')
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
           if (Gate::denies('viewAny', Product::class)) {
               abort(403, 'No tienes permiso para acceder a esta página.');
           }
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
         // Comprobar si el stock del producto está por debajo del nivel óptimo
         if ($product->stock_actual < 20)  {
            // Notificar al usuario correspondiente (ejemplo: administrador)
         
            $user = Auth::user(); 
            if ($user) {
                Notification::make()
                    ->title('El stock de ' . $product->nombre . ' es muy bajo')
                    ->body('El stock actual es de ' . $product->stock_actual . '. Revisa y actualiza el stock lo antes posible.')
                    ->sendToDatabase($user); // Enviar notificación a la base de datos del usuario
            }
        }
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
