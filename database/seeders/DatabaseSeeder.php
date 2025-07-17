<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(DepartamentosTableSeeder::class);
        $this->call(MunicipiosTableSeeder::class);
        $this->call(TiposDocumentoTableSeeder::class);
        $this->call(GeneroTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(PacientesTableSeeder::class);
    }
}
