<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departamentos')->insert([
            ['nombre' => 'Antioquia'],
            ['nombre' => 'Cundinamarca'],
            ['nombre' => 'Valle del Cauca'],
            ['nombre' => 'Santander'],
            ['nombre' => 'Bolívar']
        ]);
    }
}
