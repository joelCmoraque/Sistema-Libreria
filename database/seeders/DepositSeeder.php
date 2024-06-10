<?php

namespace Database\Seeders;
use App\Models\Deposit;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepositSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        Deposit::create([
            'nombre' => 'Deposito 1',
        ]);
        Deposit::create([
            'nombre' => 'Deposito 2',
        ]);
        Deposit::create([
            'nombre' => 'Deposito 3',
        ]);
        Deposit::create([
            'nombre' => 'Deposito 4',
        ]);
        Deposit::create([
            'nombre' => 'Deposito 5',
        ]);
    }
}
