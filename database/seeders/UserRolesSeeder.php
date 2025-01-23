<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $adminRole = Role::where('name', 'admin')->first();

        // Asignar roles a usuarios específicos
        User::where('email', 'admin@test.com')->first()->assignRole($adminRole);
    }
}
