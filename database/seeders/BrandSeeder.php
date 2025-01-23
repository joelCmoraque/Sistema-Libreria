<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $brands = [
            'no aplica',
            'MADEPA',
            'TOP',
            'LIDER',
            'MOCHILERA',
            'SAMA',
            'SUZANO',
            'CARVAJAL',
            'PAPELEX',
            'DON JAIME',
            'CANADIOS',
            'DON MOISES',
            'ABC',
            'AMERICAN IRIS',
            'EXECUTIVE',
            'HP',
            'CHAMEX',
            'GAMAS',
            'WINNER',
            'SELVA',
            'DON EUSEBIO',
            'DON WILMER',
            'ORION',
            'SABONIS',
            'MANGO',
            'F.CASTELL',
            'SKILL HANS',
            'MADISON',
            'BENMA',
            'MERLETTO',
            'PELIKAN',
            'STABILO',
            'FOSKA',
            'MILCAR',
            'MILAN',
            'ISOFIT',
            'ARTESCO',
            'MONAMI',
            'NATARAJ',
            'NOVUS',
            'PINOCHO',
            'WEX',
            'ALKALA',
            'KORES',
            'TIXO',
            'LEYDEZ',
            'KODAK',
            'TECNOPOR',
            'TALBOT',
            'FIVE STICK',
            'MONOPOL',
            'OPEN',
            'USIGN'
        ];

        foreach ($brands as $brand) {
            Brand::create([
                'nombre' => $brand,
                'descripcion' => 'sin descripción',
            ]);
        }
    }
}
