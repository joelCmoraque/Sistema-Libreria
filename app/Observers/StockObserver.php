<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\User; // Asegúrate de importar el modelo User
use Illuminate\Support\Facades\Auth; // Importa el facade Auth
use Filament\Notifications\Notification;

class StockObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        //ESTE ES EL QUE ENVIA LA NOTIFICACION modificar aqui 
           // Obtén el usuario autenticado
           $user = Auth::user(); 

           if ($user) {
               Notification::make()
                   ->title('El stock de estos productos es muy bajo')
                   ->body('Revisa los siguientes productos')
                   ->sendToDatabase($user); // Enviar notificación a la base de datos del usuario autenticado
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
