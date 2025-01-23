<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Deposit;
use App\Models\Input;
use App\Models\Output;
use App\Models\DimProducts;
use App\Models\DimDeposits;
use App\Models\DimTime;
use App\Models\FactTransactions;
use Carbon\Carbon;

class EtlComand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:etl-comand';

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
        $this->info('Starting ETL process...');

        // Cargar dimensiones
        $this->loadDimProducts();
        $this->loadDimDeposits();
        $this->loadDimTiempo();

        // Cargar hechos
        $this->loadFactTransactions();

        $this->info('ETL process completed.');
    }

    private function loadDimProducts()
    {
        $this->info('Loading DimProducts...');
        Product::chunk(100, function ($products) {
            foreach ($products as $product) {
                DimProducts::updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'codigo_unico' => $product->codigo_unico,
                        'nombre' => $product->nombre,
                        'descripcion' => $product->descripcion,
                        'unidad_medida' => $product->unidad_medida,
                        'category_id' => $product->category_id,
                        'category_nombre' => $product->category->nombre,
                        'brand_id' => $product->brand_id,
                        'brand_nombre' => $product->brand->nombre,
                        'provider_id' => $product->provider_id,
                        'provider_nombre' => $product->provider->razon_social,
                    ]
                );
            }
        });
    }

    private function loadDimDeposits()
    {
        $this->info('Loading DimDeposits...');
        Deposit::chunk(100, function ($deposits) {
            foreach ($deposits as $deposit) {
                DimDeposits::updateOrCreate(
                    ['deposit_id' => $deposit->id],
                    [
                        'nombre' => $deposit->nombre,
                        'descripcion' => $deposit->descripcion,
                    ]
                );
            }
        });
    }

    private function loadDimTiempo()
    {
        $this->info('Loading DimTiempo...');
        $startDate = Carbon::now()->subYears(5)->startOfYear();
        $endDate = Carbon::now()->endOfYear();

        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            DimTime::updateOrCreate(
                ['fecha' => $date->toDateString()],
                [
                    'año' => $date->year,
                    'mes' => $date->month,
                    'dia' => $date->day,
                    'trimestre' => ceil($date->month / 3),
                    'dia_semana' => $date->dayOfWeek,
                    'semana_año' => $date->weekOfYear,
                ]
            );
        }
    }

    private function loadFactTransactions()
    {
        $this->info('Loading FactTransactions...');

        // Procesar entradas
        Input::chunk(100, function ($inputs) {
            foreach ($inputs as $input) {
                $this->createFactTransaction($input, 'input');
            }
        });

        // Procesar salidas
        Output::chunk(100, function ($outputs) {
            foreach ($outputs as $output) {
                $this->createFactTransaction($output, 'output');
            }
        });
    }

    private function createFactTransaction($transaction, $type)
    {
        $dimProduct = DimProducts::where('product_id', $transaction->product_id)->first();
        $dimDeposit = DimDeposits::where('deposit_id', $transaction->product->deposit_id)->first();
        $dimTiempo = DimTime::where('fecha', $transaction->fecha_entrada ?? $transaction->fecha_salida)->first();

        if ($dimProduct && $dimDeposit && $dimTiempo) {
            FactTransactions::updateOrCreate(
                [
                    'product_key' => $dimProduct->product_key,
                    'deposit_key' => $dimDeposit->deposit_key,
                    'time_key' => $dimTiempo->time_key,
                    'transaction_type' => $type,
                    'reference_document' => $transaction->documento_referencia,
                ],
                [
                    'quantity' => $transaction->cantidad,
                    'unit_price' => $type == 'input' ? $transaction->compra_unitaria : $transaction->precio_unitario,
                    'total_price' => $type == 'input' ? ($transaction->cantidad * $transaction->compra_unitaria) : $transaction->total,
                ]
            );
        }
    }
}
