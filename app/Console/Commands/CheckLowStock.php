<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\User;
use Filament\Notifications\Notification;

class CheckLowStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-low-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = Product::where('stock', '<', 'optimal_stock')->get();

        if ($products->isEmpty()) {
            $this->info('No products with low stock.');
            return;
        }

        $admin = User::find(1); // Asumiendo que el administrador tiene el ID 1

        if ($admin) {
            $productNames = $products->pluck('name')->implode(', ');

            Notification::make()
                ->title('El stock de estos productos es muy bajo')
                ->body('Revisa los siguientes productos: ' . $productNames)
                ->sendToDatabase($admin); // Enviar notificación a la base de datos del administrador
        }

        $this->info('Low stock notification sent.');
    }
}
