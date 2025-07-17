<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MunicipiosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departamentos = DB::table('departamentos')->pluck('id');

        foreach ($departamentos as $departamentoId) {
            DB::table('municipios')->insert([
                ['departamento_id' => $departamentoId, 'nombre' => 'Municipio A '.$departamentoId],
                ['departamento_id' => $departamentoId, 'nombre' => 'Municipio B '.$departamentoId]
            ]);
        }
    }
}
